<?php
/**
 * User: anubis
 * Date: 24.01.14
 * Time: 17:52
 */

namespace bc\generator;


use bc\config\ConfigManager;
use bc\generator\struct\BuilderDescription;
use bc\generator\struct\ClassDescription;
use bc\generator\struct\DataMapDescription;
use bc\generator\struct\FactoryDescription;
use bc\generator\struct\ModelFieldDescription;
use bc\generator\struct\TableDescription;

class Generator {
    private $class;

    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var ClassDescription
     */
    private $model;

    public function __construct($class, $toFile = false) {
        $this->class = $class;
        $this->toFile = $toFile;
        $this->parser = new Parser(ConfigManager::get('config/generator')->get('def.path') . $this->class . '.yaml');

    }

    public function generateModel() {
        $class = new ClassDescription($this->parser->getClass(), $this->parser->getNamespace());
        $class->useDoc();
        if(!is_null($this->parser->getParent())) {
            $class->setParent($this->parser->getParent());
        }
        foreach($this->parser->getFields() as $name => $info) {
            $field = new ModelFieldDescription($name);
            if(isset($info['type'])) {
                $field->setType($info['type']);
            }
            $field->useDoc();
            if(isset($info['flags'])) {
                if(in_array('RO', $info['flags'])) {
                    $field->setReadOnly();
                }
                if(in_array('NN', $info['flags'])) {
                    $field->setRequired();
                }
            }
            $getter = '';
            if(isset($info['get'])) {
                if(isset($info['get']['name'])) {
                    $getter = $info['get']['name'];
                }
            }
            $setter = '';
            if(isset($info['set'])) {
                if(isset($info['set']['name'])) {
                    $setter = $info['set']['name'];
                }
                if(isset($info['set']['change'])) {
                    $field->setUseChanged($info['set']['change']);
                }
            }
            if(!isset($info['sqlType'])) {
                $field->setUseChanged(false);
            }
            $field->setUseGetter($getter);
            $field->setUseSetter($setter);
            $class->addField($field);
        }
        $this->model = $class;
        if($this->toFile) {
            $file = $this->parser->getPath('base') . '/' . $this->parser->getClass() . '.php';
            $writer = new ClassWriter($class, $file);
            if($writer->write() === false) {
                echo "Error: " . print_r(error_get_last(), true) . "\n";
            } else {
                echo "Saved to {$file}\n";
            }
        } else {
            echo $class->export();
        }
    }

    public function generateTable() {
        $table = new TableDescription($this->parser->getTable());
        if(!is_null($this->parser->getParentDescription())) {
            $parent = new Parser(ConfigManager::get('config/generator')->get('def.path') . $this->parser->getParentDescription() . '.yaml');
            foreach($parent->getFields() as $field => $info) {
                if(!isset($info['sqlType'])) continue;
                $default = isset($info['default']) ? $info['default'] : null;
                $table->addColumn($field, $info['sqlType'], $info['flags'], $default);
            }
        }
        foreach($this->parser->getFields() as $field => $info) {
            if(!isset($info['sqlType'])) continue;
            $default = isset($info['default']) ? $info['default'] : null;
            $table->addColumn($field, $info['sqlType'], $info['flags'], $default);
        }
        $data = $table->export();
        if($this->toFile) {
            $file = $this->parser->getPath('base') . '/' . $this->parser->getClass() . '.sql';
            file_put_contents($file, $data);
        } else {
            echo $data;
        }
    }

    public function generateDataMap() {
        $class = new DataMapDescription($this->parser->getClass() . 'DataMap', $this->parser);
        if($this->toFile) {
            $file = $this->parser->getPath('dataMap') . '/' . $this->parser->getClass() . 'DataMap.php';
            $writer = new ClassWriter($class, $file);
            if($writer->write() === false) {
                echo "Error: " . print_r(error_get_last(), true) . "\n";
            } else {
                echo "Saved to {$file}\n";
            }
        } else {
            echo $class->export();
        }
    }

    public function generateFactory() {
        $class = new FactoryDescription($this->parser);
        if($this->toFile) {
            $file = $this->parser->getPath('factory') . '/' . $this->parser->getClass() . 'Factory.php';
            $writer = new ClassWriter($class, $file);
            if($writer->write() === false) {
                echo "Error: " . print_r(error_get_last(), true) . "\n";
            } else {
                echo "Saved to {$file}\n";
            }
        } else {
            echo $class->export();
        }
    }

    public function generateBuilder() {
        $class = new BuilderDescription($this->parser, $this->model);
        if($this->toFile) {
            $file = $this->parser->getPath('builder') . '/' . $this->parser->getClass() . 'Builder.php';
            $writer = new ClassWriter($class, $file);
            $writer->addUsage($this->model->getNameForUsage());
            if($writer->write() === false) {
                echo "Error: " . print_r(error_get_last(), true) . "\n";
            } else {
                echo "Saved to {$file}\n";
            }
        } else {
            echo $class->export();
        }
    }
} 