<?php

namespace Afk11\Btctool\Console;

use Afk11\Btctool\Console\Command\AddressDecodeCommand;
use Afk11\Btctool\Console\Command\Base58Command;
use Afk11\Btctool\Console\Command\FeeRateKBCommand;
use Afk11\Btctool\Console\Command\PrivateKeyCreateCommand;
use Afk11\Btctool\Console\Command\PrivateKeyDecodeCommand;
use Afk11\Btctool\Console\Command\ScriptDecodeCommand;
use \Symfony\Component\Console\Application as ConsoleApplication;

class Application extends ConsoleApplication
{
    protected function getDefaultCommands()
    {
        $commands = parent::getDefaultCommands();

        $commands[] = new AddressDecodeCommand();
        $commands[] = new ScriptDecodeCommand();
        $commands[] = new Base58Command();
        $commands[] = new FeeRateKBCommand();
        $commands[] = new PrivateKeyCreateCommand();
        $commands[] = new PrivateKeyDecodeCommand();

        return $commands;
    }
}
