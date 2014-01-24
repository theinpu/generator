<?php
/**
 * User: anubis
 * Date: 24.01.14
 * Time: 17:28
 */

namespace bc\generator;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GeneratorCommand extends Command {

    protected function configure() {
        $this->setName('gen')
            ->addArgument('model', InputArgument::REQUIRED, "класс с моделькой для генерации")
            ->addOption('file', 'o', InputOption::VALUE_NONE, "писать в файл")
            ->addOption('table', 't', InputOption::VALUE_NONE, "генерить табличку")
            ->addOption('dataMap', 'd', InputOption::VALUE_NONE, "генерить датамап")
            ->addOption('factory', 'f', InputOption::VALUE_NONE, "генерить фабрику")
            ->addOption('builder', 'b', InputOption::VALUE_NONE, "генерить билдер")
            ->addOption('all', 'a', InputOption::VALUE_NONE, "генерить все доп.классы")
            ->setDescription("Генерация датамапов и прочих плюшек");
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $toFile = $input->getOption('file');
        $genDataMap = $input->getOption('dataMap');
        $genFactory = $input->getOption('factory');
        $genBuilder = $input->getOption('builder');
        $genTable = $input->getOption('table');
        if($input->getOption('all')) {
            $genDataMap = $genFactory = $genBuilder = $genTable = true;
        }
        $model = $input->getArgument('model');

        if($genFactory && !$genDataMap) {
            throw new \RuntimeException("Need to generate DataMap too");
        }

        $output->writeln("Checking " . $model . '...');

        $generator = new Generator($model, $toFile);
        $output->writeln('Generate model...');
        $generator->generateModel();
        $output->writeln('');
        if($genTable) {
            $output->writeln('Generate table...');
            $generator->generateTable();
            $output->writeln('');
        }
        if($genDataMap) {
            $output->writeln('Generate data map...');
            $generator->generateDataMap();
            $output->writeln('');
        }
        if($genFactory) {
            $output->writeln('Generate factory...');
            $generator->generateFactory();
            $output->writeln('');
        }
        if($genBuilder) {
            $output->writeln('Generate builder...');
            $generator->generateBuilder();
            $output->writeln('');
        }
    }

} 