<?php

namespace Afk11\Btctool\Console\Command;


use BitWasp\Buffertools\Buffer;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HexCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('hex')
            ->addArgument('hex', InputArgument::REQUIRED, 'text to encode/decode')
            ->addOption('decode', null, InputOption::VALUE_NONE, 'decode?')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $hex = $input->getArgument('hex');
        $decode = $input->getOption('decode');
        if ($decode) {
            echo Buffer::hex($hex)->getBinary() . PHP_EOL;
        } else {
            echo (new Buffer($hex))->getHex() . PHP_EOL;
        }
    }
}
