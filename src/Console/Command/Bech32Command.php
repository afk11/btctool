<?php

namespace Afk11\Btctool\Console\Command;


use BitWasp\Bitcoin\Bech32;
use BitWasp\Buffertools\Buffer;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Bech32Command extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('bech32')
            ->addArgument('bech32', InputOption::VALUE_REQUIRED, 'value to encode/decode')
            ->addOption('decode', null, InputOption::VALUE_NONE, 'decode?')
            ->addOption('hrp', null, InputOption::VALUE_REQUIRED, 'provide own HRP')
            ->addOption('network', null, InputOption::VALUE_REQUIRED, 'provide a network')
            ->addOption('testnet', null, InputOption::VALUE_NONE, 'specify testnet')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->hasOption('network')) {
            $testnet = $input->getOption("testnet");
            $networkName = $input->getOption("network");
            $network = $this->getNetwork($networkName, $testnet);
            $hrp = $network->getSegwitBech32Prefix();
        } else if ($input->hasOption('hrp')) {
            $hrp = $input->getOption('hrp');
        } else {
            throw new \RuntimeException("Must provide a network or a HRP");
        }

        $inputStr = $input->getArgument('bech32');
        $decode = $input->getOption('decode');
        if ($decode) {
            list ($gotHrp, $chars) = Bech32::decode($inputStr);
            if ($gotHrp !== $hrp) {
                throw new \RuntimeException("Mismatch between configured network's `{$hrp}` and bech32 HRP `{$gotHrp}`");
            }
            $chars = Bech32::convertBits($chars, count($chars), 5, 8, false);
            $str = '';
            foreach ($chars as $char) {
                $str .= chr($char);
            }
            echo (new Buffer($str))->getHex().PHP_EOL;
        } else {
            $buf = Buffer::hex($inputStr);
            $chars = array_values(unpack("C*", $buf->getBinary()));
            $charLen = count($chars);
            $bits = Bech32::convertBits($chars, $charLen, 8, 5);
            $encoded = Bech32::encode($hrp, $bits);
            echo $encoded.PHP_EOL;
        }
    }
}
