<?php

namespace Afk11\Btctool\Console\Command;


use BitWasp\Bitcoin\Key\PrivateKeyFactory;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PrivateKeyCreateCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('privkey:create')
            ->addOption('network', null, InputOption::VALUE_REQUIRED, 'A supported network', 'bitcoin')
            ->addOption('testnet', 't', InputOption::VALUE_NONE, 'Use testnet')
            ->addOption('compressed', 'c', InputOption::VALUE_NONE, 'Use compressed');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $networkName = $input->getOption('network');
        $testnet = $input->getOption('testnet');
        $compressed = $input->getOption('compressed');

        $privateKey = PrivateKeyFactory::create($compressed);
        $publicKey = $privateKey->getPublicKey();
        $address = $publicKey->getAddress();
        $network = $this->getNetwork($networkName, $testnet);

        $rows = [];
        $rows[] = ['wif' , $privateKey->toWif($network)];
        $rows[] = ['public key' , $publicKey->getHex()];
        $rows[] = ['address' , $address->getAddress($network)];
        
        $table = new Table($output);
        $table->setRows($rows);
        $table->render();
        return 0;
    }
}