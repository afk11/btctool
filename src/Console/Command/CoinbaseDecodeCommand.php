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

        $hasBIP9Prefix = ($header->getVersion() & (1 << 29)) != 0;

        $rows = [
            ['hash', $hash->getHex()],
            ['version', $header->getVersion()],
        ];

        if ($hasBIP9Prefix) {
            $rows[] = ['bip9_version', str_pad(decbin($header->getVersion()), 32, '0', STR_PAD_LEFT)];
        }

        $deployments = [];
        for ($i = 0; $i < 29; $i++) {
            if (($header->getVersion() & (1 << $i)) != 0) {
                $deployments[] = $i;
            }
        }

        $rows[] = ['bip9_votes', implode(", ", $deployments)];

        $rows[] =['prevHash', $header->getPrevBlock()->getHex()];
        $rows[] =['merkleRoot', $header->getMerkleRoot()->getHex()];
        $rows[] =['nTime', $header->getTimestamp()];
        $rows[] =['nBits', $header->getBits()];
        $rows[] =['bits', bin2hex(pack('N', $header->getBits()))];
        $rows[] =['nNonce', $header->getNonce()];
        $rows[] =['nTx', $nTx];

        $table = new Table($output);
        $table->setRows($rows);
        $table->render();

        return 0;
    }
}