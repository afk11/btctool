<?php

namespace Afk11\Btctool\Console\Command;


use BitWasp\Bitcoin\Base58;
use BitWasp\Bitcoin\Exceptions\Base58ChecksumFailure;
use BitWasp\Bitcoin\Exceptions\Base58InvalidCharacter;
use BitWasp\Bitcoin\Network\NetworkInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddressCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('address:decode')
            ->addArgument('address', InputArgument::REQUIRED, 'Any cryptocurrency address')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $address = $input->getArgument('address');

        if (strlen($address) < 30 || strlen($address) > 40) {
            return $this->returnError($output, 'Address does ');
        }

        try {
            $decoded = Base58::decodeCheck($address);
            if ($decoded->getSize() !== 21) {
                return $this->returnError($output, 'Base58 data had the wrong data');
            }

            $prefix = $decoded->slice(0, 1);
            $hash = $decoded->slice(1);

            $hexPrefix = $prefix->getHex();
            $maybeNetwork = null;
            $maybeType = null;
            foreach (array_keys($this->getNetworkFunctionMap()) as $netName) {
                $network = $this->getNetwork($netName);
                if ($network->getAddressByte() === $hexPrefix) {
                    list ($maybeNetwork, $maybeType) = [$netName, 'pubkeyhash'];
                } else if ($network->getP2shByte() === $hexPrefix) {
                    list ($maybeNetwork, $maybeType) = [$netName, 'scripthash'];
                }
            }

            $rows = [];
            $rows[] = ['address' , $address];
            $rows[] = ['prefix' , $hexPrefix];
            $rows[] = ['hash' , $hash->getHex()];
            if (is_string($maybeNetwork) && is_string($maybeType)) {
                $rows[] = ['network', $maybeNetwork];
                $rows[] = ['type', $maybeType];
            }

            $table = new Table($output);
            $table->setRows($rows);
            $table->render();

        } catch (Base58InvalidCharacter $e) {
            return $this->returnError($output, 'Base58 string contains invalid characters');
        } catch (Base58ChecksumFailure $e) {
            return $this->returnError($output, 'Base58 string failed checksum');
        } catch (\Exception $e) {
            return $this->returnError($output, 'An error occured: ' . $e->getMessage() ."\n".$e->getTraceAsString());
        }
    }
}