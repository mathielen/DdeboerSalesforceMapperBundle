<?php

namespace Ddeboer\Salesforce\MapperBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StatsCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this->setName('phpforce:stats')
            ->setDescription('Shows API statistics of your salesforce account')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->getContainer()->has('phpforce_salesforce.rest_client')) {
            throw new \LogicException('There is no rest client installed. Please install the rest client via "composer require phpforce/rest-client"');
        }

        $stats = $this->getContainer()->get('phpforce_salesforce.rest_client')->call('/services/data/v34.0/limits');
        foreach ($stats as $type=>$stat) {
            $output->writeln("<info>$type</info>");

            $pb = new ProgressBar($output, $stat['Max']);
            $pb->setFormat(' [%bar%] %percent:3s%% %current%/%max%');
            if ($stat['Remaining'] > 0) {
                $pb->setProgress($stat['Remaining']);
            }
            $pb->display();

            $output->writeln("");
        }
    }

}
