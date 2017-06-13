<?php

namespace Afk11\Btctool\Console\Command;


use BitWasp\Bitcoin\Block\BlockFactory;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BlockDecodeCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('block:decode')
            ->addArgument('block', InputArgument::REQUIRED, 'A bitcoin block in hex encoding')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $block = $input->getArgument('block');
        if (!ctype_xdigit($block)) {
            return $this->returnError($output, 'Block must be in hexadecimal encoding, invalid chars found');
        }

        $block = BlockFactory::fromHex($block);
        $header = $block->getHeader();
        $hash = $header->getHash();
        $txs = $block->getTransactions();
        $nTx = count($txs);
        $rows = [
            ['hash', $hash->getHex()],
            ['version', $header->getVersion()],
            ['prevHash', $header->getPrevBlock()->getHex()],
            ['merkleRoot', $header->getMerkleRoot()->getHex()],
            ['nTime', $header->getTimestamp()],
            ['nBits', $header->getBits()],
            ['bits', bin2hex(pack('N', $header->getBits()))],
            ['nNonce', $header->getNonce()],
            ['nTx', $nTx],
        ];

        $table = new Table($output);
        $table->setRows($rows);
        $table->render();

        return 0;
    }
}