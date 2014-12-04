<?php

namespace Necrogami\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BackupCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('backup')
            ->setDescription('Updates backup.phar to the latest version')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Hello Backup Users');
    }
}