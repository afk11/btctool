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
            ->addOption('compressed', 'c', InputOption::VALUE_NONE, 'Use compressed');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $networkName = $input->getOption('network');
        $testnet = $input->getOption('testnet');
        $compressed = $input->getOption('compressed');

        $network = $this->getNetwork($networkName, $testnet);
        $inner = Base58::decode("KwDiBf89QgGbjEhKnhXJuH7LrciVrZi3qYjgd9M7rFU73sVHnoWn");


        $privateKey = PrivateKeyFactory::fromHex("0000000000000000000000000000000000000000000000000000000000000001", true);//fromWif($input->getArgument('privkey'), null, $network);
        echo $privateKey->getHex().PHP_EOL;
        echo $privateKey->getInt().PHP_EOL;
        echo Bitcoin::getMath()->cmp(Bitcoin::getGenerator()->getOrder(), gmp_init($privateKey->getInt(), 10));
        echo Base58::encodeCheck(Buffer::hex('0000000000000000000000000000000000000000000000000000000000000001'));
        $publicKey = $privateKey->getPublicKey();
        $address = $publicKey->getAddress();

        $rows = [];
        $rows[] = ['wif' , $input->getArgument('privkey')];
        $rows[] = ['wif' , $privateKey->toWif(NetworkFactory::bitcoinTestnet())];
        $rows[] = ['public key' , $publicKey->getHex()];
        $rows[] = ['public key hash' , $publicKey->getPubKeyHash()->getHex()];
        $rows[] = ['address' , $address->getAddress(NetworkFactory::bitcoinTestnet())];

        $table = new Table($output);
        $table->setRows($rows);
        $table->render();
        return 0;
    }
}