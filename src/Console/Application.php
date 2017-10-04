<?php

namespace Afk11\Btctool\Console;

use Afk11\Btctool\Console\Command\AddressDecodeCommand;
use Afk11\Btctool\Console\Command\Base58Command;
use Afk11\Btctool\Console\Command\Bech32Command;
use Afk11\Btctool\Console\Command\Bip32DecodeCommand;
use Afk11\Btctool\Console\Command\BlockDecodeCommand;
use Afk11\Btctool\Console\Command\FeeRateKBCommand;
use Afk11\Btctool\Console\Command\FlipHexCommand;
use Afk11\Btctool\Console\Command\HexCommand;
use Afk11\Btctool\Console\Command\PrivateKeyCreateCommand;
use Afk11\Btctool\Console\Command\PrivateKeyDecodeCommand;
use Afk11\Btctool\Console\Command\ScriptDecodeCommand;
use Afk11\Btctool\Console\Command\TxDecodeCommand;
use \Symfony\Component\Console\Application as ConsoleApplication;

class Application extends ConsoleApplication
{
    protected function getDefaultCommands()
    {
        $commands = parent::getDefaultCommands();

        $commands[] = new AddressDecodeCommand();
        $commands[] = new ScriptDecodeCommand();
        $commands[] = new TxDecodeCommand();
        $commands[] = new BlockDecodeCommand();
        $commands[] = new Base58Command();
        $commands[] = new Bech32Command();
        $commands[] = new FlipHexCommand();
        $commands[] = new HexCommand();
        $commands[] = new Bip32DecodeCommand();
        $commands[] = new FeeRateKBCommand();
        $commands[] = new PrivateKeyCreateCommand();
        $commands[] = new PrivateKeyDecodeCommand();

        return $commands;
    }
}
