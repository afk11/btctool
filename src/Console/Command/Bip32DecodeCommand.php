<?php

namespace Afk11\Btctool\Console\Command;


use BitWasp\Bitcoin\Key\Deterministic\HierarchicalKeyFactory;
use BitWasp\Bitcoin\Key\Deterministic\HierarchicalKeySequence;
use BitWasp\Bitcoin\Key\PrivateKeyFactory;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Bip32DecodeCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('bip32:decode')
            ->addArgument('key', InputArgument::REQUIRED, 'HD key')
            ->addOption('network', null, InputOption::VALUE_REQUIRED, 'A supported network', 'bitcoin')
            ->addOption('testnet', 't', InputOption::VALUE_NONE, 'Use testnet')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $key = $input->getArgument('key');
        $networkName = $input->getOption('network');
        $testnet = $input->getOption('testnet');

        $network = $this->getNetwork($networkName, $testnet);
        $bip32 = HierarchicalKeyFactory::fromExtended($key, $network);

        $rows = [];
        $rows[] = ['bytes' , $bip32->isPrivate() ? $network->getHDPrivByte() : $network->getHDPubByte()];
        $rows[] = ['depth' , $bip32->getDepth()];
        $rows[] = ['fpr' , $bip32->getFingerprint()];

        $hardened = $bip32->isHardened();
        $rows[] = ['hardened' , $hardened ? 'true' : 'false'];
        if ($hardened) {
            $rows[] = ['hardened idx' , $bip32->getSequence() - (2 << 31-1)];
        }

        $rows[] = ['address index' , $bip32->getSequence()];

        $rows[] = ['chain code' , $bip32->getChainCode()->getHex()];
        $rows[] = ['public key' , $bip32->getPublicKey()->getHex()];
        if ($bip32->isPrivate()) {
            $rows[] = ['private key' , $bip32->getPrivateKey()->getHex()];
        }

        $table = new Table($output);
        $table->setRows($rows);
        $table->render();
        return 0;
    }
}