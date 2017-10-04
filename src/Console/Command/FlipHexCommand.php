<?php

namespace Afk11\Btctool\Console\Command;


use BitWasp\Buffertools\Buffer;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FlipHexCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('hex:flip')
            ->addArgument('hex', null, InputOption::VALUE_REQUIRED, 'hex string')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $base58 = $input->getArgument('hex');
        $buffer = Buffer::hex($base58);
        echo $buffer->flip()->getHex().PHP_EOL;
    }
}
