<?php
/**
 * User: anubis
 * Date: 10.04.14
 * Time: 0:04
 */

namespace bc\generator;

use bc\config\ConfigManager;
use bc\generator\struct\TableDescription;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BatchCommand extends Command {

    private $defPath;
    private $map = array();

    protected function configure() {
        $this->setName('batch')
             ->setDescription("пакетная генерация")
             ->addOption('dir', 'd', InputOption::VALUE_OPTIONAL, 'откуда генерим', '.')
             ->addOption('sql', 't', InputOption::VALUE_NONE, 'генерить sql')
             ->addOption('container', 'c', InputOption::VALUE_NONE, 'генерить контейнер для фабрик')
             ->addOption('json', 'j', InputOption::VALUE_NONE, 'генерить экспорт в json');
    }
    
    private function getBinPath(){
        
        $path = 'bin/';
        
        return $path;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $sql = $input->getOption('sql');
        $json = $input->getOption('json');
        $container = $input->getOption('container');

        $cfg = ConfigManager::get('config/generator');
        $this->defPath = $cfg->get('def.path');
        $dir = $input->getOption('dir');
        $items = $this->getDescriptions($this->defPath.trim($dir, '/').'/');
        $controllers = $this->getControllers($items);
        $models = $this->getModels($items);

        $output->writeln('Generating controllers ('.count($controllers).')...');

        foreach($controllers as $controller) {
            exec('./bin/gen ctrl -ocr '.$controller, $out);
            if($output->getVerbosity() == OutputInterface::VERBOSITY_VERY_VERBOSE) {
                echo $out;
            }
        }

        $output->writeln('Generating models ('.count($models).')...');
        $out = array();

        $tables = array();

        foreach($models as $model) {
            $params = array('o', 'm', 'd', 'f', 'b');
            if($json) $params[] = 'j';
            $cmd = '-'.implode('', $params);
            
            $binPath = 'php ' . $this->getBinPath() . 'gen ';
            $command = $binPath . 'model '.$cmd.' '.$model['cmd'];

            exec($command, $out);
            if($output->getVerbosity() == OutputInterface::VERBOSITY_VERY_VERBOSE) {
                echo $out;
            }
            $table = $model['data']['table'];
            if($sql && !isset($tables[$table])) {
                $tables[$table] = $model['data'];
            }
        }

        if($sql) {
            $output->writeln('Generating sql ('.count($tables).')..');

            foreach($tables as $table => $model) {
                $output->writeln('generate table '.$table.' from '.$model['name']);
                $parser = new Parser($model);
                $item = new TableDescription($table);

                $p = clone $parser;

                while(!is_null($p->getParentParser())) {
                    $p = $p->getParentParser();
                    foreach($p->getFields() as $field => $info) {
                        if(is_null($info->getSqlType())) continue;
                        $default = $info->getDefault();
                        $item->addColumn($field, $info->getSqlType(), $info->getFlags(), $default);
                    }
                }

                foreach($parser->getFields() as $field => $info) {
                    if(is_null($info->getSqlType())) continue;
                    $default = $info->getDefault();
                    $item->addColumn($field, $info->getSqlType(), $info->getFlags(), $default);
                }
                $file = $parser->getSavePath().'sql/'.$table.'.sql';
                $path = dirname($file);
                if(!file_exists($path)) {
                    mkdir($path);
                }
                file_put_contents($file, $item->export(false));
            }
        }

        if($container) {
            $output->writeln('Generating factory container...');
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
        $parser = new \Symfony\Component\Yaml\Parser();
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
        $parser = new \Symfony\Component\Yaml\Parser();
        foreach($items as $item) {
            $data = $parser->parse(file_get_contents($item));
            if(isset($data['fields'])) {
                $models[$data['class']] = array(
                    'cmd'  => str_replace($this->defPath, '', $item),
                    'data' => $data
                );
            }
        }

        return $models;
    }
}