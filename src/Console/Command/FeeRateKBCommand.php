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

class FeeRateKBCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('feerate:kb')
            ->addArgument('rate', null, InputOption::VALUE_REQUIRED, 'fee rate (BTC/sat)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $rate = $input->getArgument('rate');
        echo ceil($rate * 100000000 / 1000);
    }
}
