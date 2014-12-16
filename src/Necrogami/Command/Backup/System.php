<?php

namespace Necrogami\Command\Backup;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Aws\S3\S3Client;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Adapter\AwsS3;
use Necrogami\Config;

class System extends Command
{
	function configure()
	{
		$this
            ->setName('backup:system')
            ->setDescription('Creates a new Backup')
        ;
	}

	function execute(InputInterface $input, OutputInterface $output)
	{
		$config = Config::getInstance();
		$client = S3Client::factory($config->get('aws'));

		$lfs = new Filesystem(new Local('/'));
		$sfs = new Filesystem(new AwsS3($client, $config->get('aws.bucket')));
		$date = date("Ymd");
		$output->writeln('Reading /etc/passwd and writing to S3');
		$contents = $sfs->write("/$date/anton/etc/passwd", $lfs->read("/etc/passwd"));
		print_r($contents);
		$output->writeln('Wrote to S3');
	}
}