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

class ModelGeneratorCommand extends Command {

    protected function configure() {
        $this->setName('model')
             ->addArgument('modelName', InputArgument::REQUIRED, "класс с моделькой для генерации")
             ->addOption('file', 'o', InputOption::VALUE_NONE, "писать в файл")
             ->addOption('model', 'm', InputOption::VALUE_NONE, "генерить модель")
             ->addOption('table', 't', InputOption::VALUE_NONE, "генерить табличку")
             ->addOption('dataMap', 'd', InputOption::VALUE_NONE, "генерить датамап")
             ->addOption('factory', 'f', InputOption::VALUE_NONE, "генерить фабрику")
             ->addOption('builder', 'b', InputOption::VALUE_NONE, "генерить билдер")
             ->addOption('json', 'j', InputOption::VALUE_NONE, "генерить представление в json")
             ->addOption('all', 'a', InputOption::VALUE_NONE, "генерить все доп.классы")
             ->setDescription("Генерация датамапов и прочих плюшек");
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $toFile = $input->getOption('file');
        $genModel = $input->getOption('model');
        $genDataMap = $input->getOption('dataMap');
        $genFactory = $input->getOption('factory');
        $genBuilder = $input->getOption('builder');
        $genTable = $input->getOption('table');
        $genJSON = $input->getOption('json');
        if($input->getOption('all')) {
            $genModel
                = $genDataMap
                = $genFactory
                = $genBuilder
                = $genTable
                = $genJSON
                = true;
        }
        $model = $input->getArgument('modelName');

        if($genFactory && !$genDataMap) {
            throw new \RuntimeException("Need to generate DataMap too");
        }

        $output->writeln("Checking ".$model.'...');

        $generator = new Generator($model, $toFile);
        $generator->setGenerateJSON($genJSON);
        if($genModel) {
            $output->writeln('Generate model...');
            $generator->generateModel();
            $output->writeln('');
        }
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