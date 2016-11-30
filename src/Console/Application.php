<?php

namespace Afk11\Btctool\Console;

use Afk11\Btctool\Console\Command\AddressCommand;
use Afk11\Btctool\Console\Command\PrivateKeyCreateCommand;
use \Symfony\Component\Console\Application as ConsoleApplication;

class Application extends ConsoleApplication
{
    protected function getDefaultCommands()
    {
        $commands = parent::getDefaultCommands();

        $commands[] = new AddressCommand();
        $commands[] = new PrivateKeyCreateCommand();

        return $commands;
    }
}