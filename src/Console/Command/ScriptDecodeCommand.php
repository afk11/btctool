<?php

namespace Afk11\Btctool\Console\Command;


use BitWasp\Bitcoin\Address\AddressFactory;
use BitWasp\Bitcoin\Script\ScriptFactory;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ScriptDecodeCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('script:decode')
            ->addArgument('script', InputArgument::REQUIRED, 'A bitcoin script in hex encoding')
            ->addOption('network', null, InputOption::VALUE_REQUIRED, 'Network for addresses', 'bitcoin')
            ->addOption('testnet', 't', InputOption::VALUE_NONE, 'Use testnet')
        ;
    }

    private function splitChunkString($string)
    {
        
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $network = $this->getNetwork($input->getOption('network'), $input->getOption('testnet'));
        $scriptHex = $input->getArgument('script');
        if (!ctype_xdigit($scriptHex)) {
            return $this->returnError($output, 'Script must be in hexadecimal encoding, invalid chars found');
        }

        $script = ScriptFactory::fromHex($scriptHex);
        $parser = $script->getScriptParser();
        $asm = $parser->getHumanReadable();

        $p2sh = ScriptFactory::scriptPubKey()->p2sh($script->getScriptHash());
        $p2wsh = ScriptFactory::scriptPubKey()->p2wsh($script->getWitnessScriptHash());

        $rows = [];
        $rows[] = ['hex' , $scriptHex];
        $rows[] = ['asm' , $asm];
        $rows[] = ['script hash' , $script->getScriptHash()->getHex()];
        $rows[] = ['P2SH address' , AddressFactory::fromScript($script)->getAddress($network)];
        $rows[] = ['P2SH script' , $p2sh->getHex()];
        $rows[] = ['P2WSH script' , $p2wsh->getHex()];

        $table = new Table($output);
        $table->setRows($rows);
        $table->render();

        return 0;
    }
}