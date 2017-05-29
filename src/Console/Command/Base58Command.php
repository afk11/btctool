<?php

namespace Afk11\Btctool\Console\Command;


use BitWasp\Bitcoin\Base58;
use BitWasp\Buffertools\Buffer;
use BitWasp\Bitcoin\Exceptions\Base58ChecksumFailure;
use BitWasp\Bitcoin\Exceptions\Base58InvalidCharacter;
use BitWasp\Bitcoin\Network\NetworkInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Base58Command extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('base58')
            ->addArgument('base58', null, InputOption::VALUE_NONE, 'decode?')
            ->addOption('decode', null, InputOption::VALUE_NONE, 'decode?')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $base58 = $input->getArgument('base58');
        $decode = $input->getOption('decode');
        if ($decode) {
            echo Base58::decode($base58)->getHex() . PHP_EOL;
        } else {
            echo Base58::encode(Buffer::hex($base58)) . PHP_EOL;
        }
    }
}
