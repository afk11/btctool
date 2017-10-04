<?php

namespace Afk11\Btctool\Console\Command;


use BitWasp\Bitcoin\Address\AddressFactory;
use BitWasp\Bitcoin\Address\PayToPubKeyHashAddress;
use BitWasp\Bitcoin\Address\SegwitAddress;
use BitWasp\Bitcoin\Crypto\Hash;
use BitWasp\Bitcoin\Script\ScriptType;
use BitWasp\Bitcoin\Network\NetworkInterface;
use BitWasp\Bitcoin\Script\ScriptInfo\Multisig;
use BitWasp\Bitcoin\Script\Classifier\OutputClassifier;
use BitWasp\Bitcoin\Script\ScriptInterface;
use BitWasp\Bitcoin\Script\WitnessProgram;
use BitWasp\Bitcoin\Transaction\TransactionFactory;
use BitWasp\Bitcoin\Transaction\TransactionInputInterface;
use BitWasp\Bitcoin\Transaction\TransactionInterface;
use BitWasp\Bitcoin\Transaction\TransactionOutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TxDecodeCommand extends AbstractCommand
{
    /**
     * @var OutputClassifier
     */
    private $classifier;

    /**
     * @var NetworkInterface
     */
    private $network;

    /**
     * TxDecodeCommand constructor.
     * @param null $name
     */
    public function __construct($name = null)
    {
        $this->classifier = new OutputClassifier();
        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setName('tx:decode')
            ->addArgument('tx', InputArgument::REQUIRED, 'A bitcoin script in hex encoding')
            ->addOption('network', null, InputOption::VALUE_REQUIRED, 'Network for addresses', 'bitcoin')
            ->addOption('testnet', 't', InputOption::VALUE_NONE, 'Set testnet flag for network')
        ;
    }

    /**
     * @param ScriptInterface $script
     * @return array
     */
    public function baseScriptDetail(ScriptInterface $script)
    {
        return [
            "asm" => $script->getScriptParser()->getHumanReadable(),
            "hex" => $script->getHex(),
        ];
    }

    /**
     * @param ScriptInterface $script
     * @return array
     */
    public function outScriptDetail(ScriptInterface $script)
    {
        $detail = $this->classifier->decode($script);
        $type = $detail->getType();
        $extra = [
            'type' => $type,
        ];

        if ($type === ScriptType::P2PK || $type === ScriptType::P2PKH) {
            $extra['reqSigs'] = 1;
            $extra['addresses'] = [
                AddressFactory::fromOutputScript($script)->getAddress($this->network)
            ];
        }

        if ($type === ScriptType::MULTISIG) {
            $multisig = new Multisig($script);
            $addresses = [];
            foreach ($multisig->getKeyBuffers() as $key) {
                $keyHash = Hash::sha256ripe160($key);
                $addresses[] = (new PayToPubKeyHashAddress($keyHash))->getAddress($this->network);
            }

            $extra['addresses'] = $addresses;
        }

        $wp = null;
        if ($script->isWitness($wp)) {
            /** @var WitnessProgram $wp */
            $extra['addresses'] = [
                (new SegwitAddress($wp))->getAddress($this->network),
            ];
        }

        return array_merge(
            $this->baseScriptDetail($script),
            $extra
        );
    }

    /**
     * @param TransactionInputInterface $txin
     * @return array
     */
    public function parseInput(TransactionInputInterface $txin)
    {
        return [
            "txid" => $txin->getOutPoint()->getTxId()->getHex(),
            "vout" => (int)$txin->getOutPoint()->getVout(),
            "scriptSig" => $this->baseScriptDetail($txin->getScript()),
            "sequence" => (int)$txin->getSequence(),
        ];
    }

    /**
     * @param TransactionOutputInterface $txOut
     * @return array
     */
    public function parseOutput(TransactionOutputInterface $txOut)
    {
        return [
            "value" => (int)$txOut->getValue(),
            "scriptPubKey" => $this->outScriptDetail($txOut->getScript()),
        ];
    }

    /**
     * @param TransactionInterface $tx
     * @return array
     */
    public function parseTransaction(TransactionInterface $tx)
    {
        $txid = $tx->getTxId();
        $witSer = $tx->getWitnessSerialization();
        $hash = Hash::sha256d($witSer);
        $size = $tx->getBuffer()->getSize();
        $vsize = ceil(($size * 3 + $witSer->getSize()) / 4);

        $inputs = array_map([$this, 'parseInput'], $tx->getInputs());
        $nIn = count($inputs);
        for ($i = 0; $i < $nIn; $i++) {
            $witness = $tx->getWitness($i);
            $wit = [];
            foreach ($witness->all() as $val) {
                $wit[] = $val->getHex();
            }
            $inputs[$i]['witness'] = $wit;
        }

        return [
            "txid" => $txid->getHex(),
            "hash" => $hash->getHex(),
            'version' => (int) $tx->getVersion(),
            'size' => $size,
            'vsize' => $vsize,
            'locktime' => $tx->getLockTime(),
            'vin' => $inputs,
            'vout' => array_map([$this, 'parseOutput'], $tx->getOutputs()),
        ];
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $network = $this->getNetwork($input->getOption('network'));
        $this->network = $network;

        $tx = $input->getArgument('tx');
        if (!ctype_xdigit($tx)) {
            return $this->returnError($output, 'TX must be in hexadecimal encoding, invalid chars found');
        }

        $tx = TransactionFactory::fromHex($tx);
        echo json_encode($this->parseTransaction($tx), JSON_PRETTY_PRINT) . PHP_EOL;

        return 0;
    }
}
