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
use bc\generator\struct\MethodDescription;
use bc\generator\struct\ModelFieldDescription;
use bc\generator\struct\TableDescription;

class Generator {

    private $class;
    private $config;

    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var ClassDescription
     */
    private $model;
    private $genJSON = false;

    public function __construct($class, $toFile = false) {
        $this->class = $class;
        $this->toFile = $toFile;
        $this->config = ConfigManager::get('config/generator');
        $this->parser = new Parser($this->config->get('def.path').$this->class);

    }

    public function generateModel() {
        $class = new ClassDescription($this->parser->getClass(), $this->parser->getNamespace());
        $class->useDoc();
        $hasParent = false;
        if(!is_null($this->parser->getParent())) {
            $class->setParent($this->parser->getParent());
        }
        if(!is_null($this->parser->getParentDescription())) {
            $hasParent = true;
        }
        $fields = array();

        if(!$hasParent) {
            $fields[] = "'id' => \$this->getId()";
        } else {
            if($this->parser->getParentDescription() == 'model') {
                $fields[] = "'id' => \$this->getId()";
            }
        }
        foreach($this->parser->getFields() as $name => $info) {
            $field = new ModelFieldDescription($name);
            $field->setModifier('private');
            if($info->hasType()) {
                $field->setType($info->getType());
            }
            $field->useDoc();
            if($info->isReadOnly()) {
                $field->setReadOnly();
            }
            if($info->isRequired()) {
                $field->setRequired();
            }

            $field->setUseChanged($info->useChange());
            if(is_null($info->getSqlType())) {
                $field->setUseChanged(false);
            }
            $field->setUseGetter($info->getter());
            $field->setUseSetter($info->setter());
            $class->addField($field);
            if(!in_array('SO', $info->getFlags())) {
                $fields[] = "'{$name}' => \$this->".$field->getGetter()->getName().'()';
            }
        }
        if($this->genJSON) {
            if(!$hasParent) {
                $class->addInterface('JSONExport');
            } else {
                if($this->parser->getParentDescription() == 'model') {
                    $class->addInterface('JSONExport');
                }
            }
            $arrayExport = new MethodDescription('getArray');
            $arrayExport->setType('array');
            if($hasParent && $this->parser->getParentDescription() != 'model') {
                $arrayExport->appendCode("return array(\n".implode(",\n", $fields)."\n) + parent::getArray();");
            }
            else {
                $arrayExport->appendCode("return array(\n".implode(",\n", $fields)."\n);");
            }
            $class->addMethod($arrayExport);

            if(!$hasParent) {
                $jsonExport = new MethodDescription('getJSON');
                $jsonExport->setType('string');
                $jsonExport->appendCode("return json_encode(\$this->getArray());");
                $class->addMethod($jsonExport);
            }
        }
        $this->model = $class;
        if($this->toFile) {
            $path = $this->parser->generatePath($this->parser->getFullClass());
            $file = $this->config->get('save.path').$path['path'].'/'.$path['file'];
            $writer = new ClassWriter($class, $file);
            if($writer->write() === false) {
                echo "Error: ".print_r(error_get_last(), true)."\n";
            }
            else {
                echo "Saved to ".realpath($file)."\n";
            }
        }
        else {
            echo $class->export(false);
        }
    }

    public function generateTable() {
        $table = new TableDescription($this->parser->getTable());
        if(!is_null($this->parser->getParentDescription())) {
            $parent = new Parser(ConfigManager::get('config/generator')
                                              ->get('def.path').$this->parser->getParentDescription());
            foreach($parent->getFields() as $field => $info) {
                if(is_null($info->getSqlType())) continue;
                $default = $info->getDefault();
                $table->addColumn($field, $info->getSqlType(), $info->getFlags(), $default);
            }
        }
        foreach($this->parser->getFields() as $field => $info) {
            if(is_null($info->getSqlType())) continue;
            $default = $info->getDefault();
            $table->addColumn($field, $info->getSqlType(), $info->getFlags(), $default);
        }
        $data = $table->export(false);
        if($this->toFile && !$this->parser->isAbstract()) {
            $path = $this->parser->generatePath($this->parser->getFullClass(), 'sql');
            $file = $this->config->get('save.path').$path['path'].'/'.$path['file'];

            if(file_exists($file)) {
                unlink($file);
            }

            if(file_put_contents($file, $data) > 0) {
                echo "Saved to ".realpath($file)."\n";
            }
            else {
                echo "Error: ".print_r(error_get_last(), true)."\n";
            }
        }
        else {
            echo $data;
        }
    }

    public function generateDataMap() {
        $class = new DataMapDescription($this->parser->getClass().'DataMap', $this->parser);
        if($this->toFile) {
            $path = $this->parser->generatePath(
                                 $class->getNamespace()
                                 .$this->parser->getPath('dataBase')
                                 .'/'.$this->parser->getClass().'DataMap'
            );
            $file = $this->config->get('save.path').$path['path'].'/'.$path['file'];
            $writer = new ClassWriter($class, $file);
            if($class->getNamespace() != $this->model->getNamespace()) {
                $writer->addUsage($this->model->getNameForUsage());
            }
            if($writer->write() === false) {
                echo "Error: ".print_r(error_get_last(), true)."\n";
            }
            else {
                echo "Saved to ".realpath($file)."\n";
            }
        }
        else {
            echo $class->export(false);
        }
    }

    public function generateFactory() {
        $class = new FactoryDescription($this->parser);
        if($this->toFile) {
            $path = $this->parser->generatePath(
                                 $class->getNamespace()
                                 .$this->parser->getPath('factory')
                                 .'/'.$this->parser->getClass().'Factory'
            );
            $file = $this->config->get('save.path').$path['path'].'/'.$path['file'];
            $writer = new ClassWriter($class, $file);
            if($class->getNamespace() != $this->model->getNamespace()) {
                $writer->addUsage($this->model->getNameForUsage());
            }
            if($writer->write() === false) {
                echo "Error: ".print_r(error_get_last(), true)."\n";
            }
            else {
                echo "Saved to ".realpath($file)."\n";
            }
        }
        else {
            echo $class->export(false);
        }
    }

    public function generateBuilder() {
        $class = new BuilderDescription($this->parser, $this->model);
        if($this->toFile) {
            $path = $this->parser->generatePath(
                                 $class->getNamespace()
                                 .$this->parser->getPath('builder')
                                 .'/'.$this->parser->getClass().'Builder'
            );
            $file = $this->config->get('save.path').$path['path'].'/'.$path['file'];
            $writer = new ClassWriter($class, $file);
            if($class->getNamespace() != $this->model->getNamespace()) {
                $writer->addUsage($this->model->getNameForUsage());
            }
            if($writer->write() === false) {
                echo "Error: ".print_r(error_get_last(), true)."\n";
            }
            else {
                echo "Saved to ".realpath($file)."\n";
            }
        }
        else {
            echo $class->export(false);
        }
    }

    public function setGenerateJSON($genJSON) {
        $this->genJSON = $genJSON;
    }
}