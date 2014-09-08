#!/usr/bin/env php

<?php

require_once __DIR__ . "/../application/configs/config.php";

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\ClassLoader\UniversalClassLoader;
use Symfony\Component\Console\Helper\ProgressBar;

$console = new Application();


$console
    ->register('elsaticsearch:rebuild')
    ->setDefinition(array(new InputArgument('type', InputArgument::REQUIRED, 'Type to rebuild')))
    ->setDescription('Rebuild index into elasticsearch')
    ->setCode(function (InputInterface $input, OutputInterface $output) {
        $type = $input->getArgument('type');

        if (!in_array($type, array('products', 'selection'))) {
            throw new InvalidArgumentException("type should be products or selection");
        }

        $output->writeln("Start generate index for <info>$type</info>");

        $createEnvironment = new ContextSearch_CreateEnvironment();

        $loaderFactory = new ContextSearch_LoaderFactory();
        $elasticSearchModel = $loaderFactory->getModelElasticSearch();
        $countElementSQL = $elasticSearchModel->getAllData(true);

        $output->setDecorated(true);

        $count = $elasticSearchModel->getConnectDB()->fetchOne($countElementSQL);

        $progress = new ProgressBar($output, $count);

        $progress->setFormat('%message% %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');

        $progress->setMessage('Start');
        $progress->start();

        if ($type == 'products') {
            $generator = new ContextSearch_RebuildProducts();
        } elseif ($type == 'selection') {
            $generator = new ContextSearch_RebuildFilters();
        }


        $generator->run($progress, $loaderFactory, $createEnvironment);

        $progress->setMessage('Task is finished');
        $progress->finish();

    });

$console->run();