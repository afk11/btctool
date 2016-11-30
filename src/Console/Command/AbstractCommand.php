<?php

namespace Afk11\Btctool\Console\Command;

use BitWasp\Bitcoin\Network\NetworkFactory;
use BitWasp\Bitcoin\Network\NetworkInterface;
use \Symfony\Component\Console\Command\Command as ConsoleCommand;
use Symfony\Component\Console\Output\OutputInterface;

class AbstractCommand extends ConsoleCommand
{
    private $networkFunctionMap;
    protected $consideredNetworks = ['bitcoin', 'litecoin', 'viacoin'];
    /**
     * @param OutputInterface $output
     * @param string $message
     * @return int
     */
    protected function returnError(OutputInterface $output, $message)
    {
        $output->writeln("<error>{$message}</error>");
        return 1;
    }

    /**
     * @return string[]
     */
    public function getNetworkFunctionMap()
    {
        if ($this->networkFunctionMap === null) {
            $supported = $this->consideredNetworks;
            $map = [];
            foreach ($supported as $name) {
                $map[$name] = $name;
                $map[$name . '-testnet'] = $name . 'Testnet';
            }

            $this->networkFunctionMap = $map;
        }

        return $this->networkFunctionMap;
    }

    /**
     * @param string $network
     * @return NetworkInterface
     */
    public function getNetwork($network, $testnet = false)
    {
        if ($testnet) {
            $network .= '-testnet';
        }

        $map = $this->getNetworkFunctionMap();
        if (!isset($map[$network])) {
            throw new \RuntimeException('Unknown network');
        }
        $fxn = $map[$network];
        return NetworkFactory::$fxn();
    }
}