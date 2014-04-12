<?php
/**
 * User: anubis
 * Date: 10.04.14
 * Time: 0:04
 */

namespace bc\generator;

use bc\config\ConfigManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Parser;

class BatchCommand extends Command {

    private $defPath;

    protected function configure() {
        $this->setName('batch')
             ->setDescription("пакетная генерация")
             ->addOption('dir', 'd', InputOption::VALUE_OPTIONAL, 'откуда генерим', '.')
             ->addOption('sql', 't', InputOption::VALUE_NONE, 'генерить sql')
             ->addOption('json', 'j', InputOption::VALUE_NONE, 'генерить экспорт в json');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $sql = $input->getOption('sql');
        $json = $input->getOption('json');

        $cfg = ConfigManager::get('config/generator');
        $this->defPath = $cfg->get('def.path');
        $dir = $input->getOption('dir');
        $items = $this->getDescriptions($this->defPath.trim($dir, '/').'/');
        $controllers = $this->getControllers($items);
        $models = $this->getModels($items);

        $output->writeln('Generating controllers ('.count($controllers).')...');

        foreach($controllers as $controller) {
            exec('./g ctrl -ocr '.$controller, $out);
            if($output->getVerbosity() == OutputInterface::VERBOSITY_VERY_VERBOSE) {
                $output->writeln($out);
            }
        }

        $output->writeln('Generating models ('.count($models).')...');
        $out = array();

        foreach($models as $model) {
            $params = array('o', 'm', 'd', 'f', 'b');
            if($json) $params[] = 'j';
            $cmd = '-'.implode('', $params);
            exec('./g model '.$cmd.' '.$model, $out);
            if($output->getVerbosity() == OutputInterface::VERBOSITY_VERY_VERBOSE) {
                $output->writeln($out);
            }
        }
    }

    /**
     * @param $path
     *
     * @return array
     */
    private function getDescriptions($path) {
        $dir = dir($path);
        $items = array();

        while(false != ($entry = $dir->read())) {
            if($entry == '..' || $entry == '.') continue;
            if($entry == 'model.yaml') continue;
            $item = $path.$entry;
            if(is_dir($item)) {
                $items = array_merge($items, $this->getDescriptions($item.'/'));
            }
            else {
                if(strpos($entry, '.yaml') === false) continue;
                $items[] = $item;
            }
        }

        return $items;
    }

    private function getControllers($items) {
        $controllers = array();
        $parser = new Parser();
        foreach($items as $item) {
            $data = $parser->parse(file_get_contents($item));
            if(isset($data['actions'])) {
                $controllers[] = str_replace($this->defPath, '', $item);
            }
        }

        return $controllers;
    }

    private function getModels($items) {
        $models = array();
        $parser = new Parser();
        foreach($items as $item) {
            $data = $parser->parse(file_get_contents($item));
            if(isset($data['fields'])) {
                $models[] = str_replace($this->defPath, '', $item);
            }
        }

        return $models;
    }
}