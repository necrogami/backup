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
    /**
     * Configures command for use with Symfony Command
     *
     * @return void
     */
    function configure()
    {
        $this
            ->setName('backup:system')
            ->setDescription('Creates a new Backup');
    }
    /**
     * Executes for use with Symfony Command
     * 
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    function execute(InputInterface $input, OutputInterface $output)
    {
        //AWS Config Setup
        $config = Config::getInstance();
        $client = S3Client::factory($config->get('aws'));

        //Filesystem Seup
        $lfs = new Filesystem(new Local('/'));
        $sfs = new Filesystem(new AwsS3($client, $config->get('aws.bucket')));

        $date = date("Ymd");

        //Backup Setup get Directories and Files
        $directories = $config->get('backup.directories');
        $files = $config->get('backup.files');
        $server = $config->get('server.name');

        $file = [];

        //Loop through Directories and add files to array
        if(isset($directories) && is_array($directories))
        {
            foreach($directories as $dir)
            {
                $di = new \RecursiveDirectoryIterator($dir);
                foreach (new \RecursiveIteratorIterator($di) as $filename => $info) {
                    $name = explode('/',$filename);
                    $name = end($name);
                    if($name != '.' && $name != '..')
                        $file[] = $filename;
                }
            }
        }
        //Append files to end of main files array
        if(isset($files) && is_array($files))
        {
            foreach($files as $info)
            {
                $file[] = $info;
            }
        }
        //Loop through main files list and send them to S3
        foreach($file as $info)
        {
            $output->writeln('Reading '. $info. ' and writing to S3');
            $contents = $sfs->write("/".$date."/".$server.$info, $lfs->read($info));
            if($contents = 1)
                $output->writeln('Wrote to S3');
        }
    }
}