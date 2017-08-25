<?php

namespace Afk11\Btctool\Console\Command;


use BitWasp\Bitcoin\Base58;
use BitWasp\Bitcoin\Bitcoin;
use BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Adapter\EcAdapter;
use BitWasp\Bitcoin\Key\PrivateKeyFactory;
use BitWasp\Bitcoin\Network\NetworkFactory;
use BitWasp\Buffertools\Buffer;
use BitWasp\Buffertools\Buffertools;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PrivateKeyDecodeCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('privkey:decode')
            ->addArgument('privkey', null, InputArgument::REQUIRED)
            ->addOption('network', null, InputOption::VALUE_REQUIRED, 'A supported network', 'bitcoin')
            ->addOption('testnet', 't', InputOption::VALUE_NONE, 'Use testnet')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $networkName = $input->getOption('network');
        $testnet = $input->getOption('testnet');

        $network = $this->getNetwork($networkName, $testnet);

        $privateKey = PrivateKeyFactory::fromWif($input->getArgument('privkey'), null, $network);
        $publicKey = $privateKey->getPublicKey();
        $address = $publicKey->getAddress();

        $rows = [];
        $rows[] = ['wif' , $input->getArgument('privkey')];
        $rows[] = ['hex' , $privateKey->getHex()];
        $rows[] = ['public key' , $publicKey->getHex()];
        $rows[] = ['public key hash' , $publicKey->getPubKeyHash()->getHex()];
        $rows[] = ['address' , $address->getAddress(NetworkFactory::bitcoinTestnet())];

        $table = new Table($output);
        $table->setRows($rows);
        $table->render();
        return 0;
    }
}