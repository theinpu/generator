<?php
/**
 * User: inpu
 * Date: 26.10.13
 * Time: 0:07
 */

namespace bc\generator\struct;

use bc\generator\Parser;

class BuilderDescription extends ClassDescription {

    private $fullClassName;
    /**
     * @var Parser
     */
    private $parser;
    /**
     * @var ClassDescription
     */
    private $model;

    /**
     * @param Parser $parser
     * @param ClassDescription $model
     */
    public function __construct($parser, $model) {
        $name = $parser->getClass() . 'Builder';
        $namespace = $parser->getNamespace() . $parser->getPath('builder', true);
        $this->parser = $parser;
        $this->model = $model;
        $this->fullClassName = $this->parser->getNamespace() . '\\' . $this->parser->getClass();
        parent::__construct($name, $parser , $namespace);
        $this->addInterface('IBuilder');
        $this->useDoc();
        $this->insertFields();
        $this->insertCreate();
        $this->insertBuild();
    }

    private function insertFields() {
        foreach($this->parser->getFields() as $name => $info) {
            if($name == 'id') continue;
            $field = new FieldDescription($name, $info->getType());
            $field->setModifier('private');
            $field->setUseSetter($name);
            $code = "return \$this;";
            $field->getSetter()->setType($this->parser->getClass() . 'Builder');
            $field->getSetter()->appendCode($code);
            $this->addField($field);
        }
    }

    private function insertCreate() {
        $create = new MethodDescription('create');
        $create->setType($this->getName());
        $create->setStatic(true);
        $create->appendCode('return new self();');
        $this->addMethod($create);
    }

    private function insertBuild() {
        $build = new MethodDescription('build');
        $build->setType($this->parser->getClass());
        $code = array();
        /** @var ModelFieldDescription[] $fields */
        $fields = $this->model->getFields();
        foreach($fields as $field) {
            if($field->isRequired()) {
                $code[] = 'if(is_null($this->' . $field->getName() . ')) {';
                $code[] = "\tthrow new \\InvalidArgumentException('Need to set " . $field->getName() . "');";
                $code[] = "}";
            }
        }
        $code[] = '$item = new ' . $this->parser->getClass() . "();";
        foreach($this->model->getFields() as $field) {
            if($field->getName() == 'id') continue;
            $code[] = '$item->' . $field->setter() . '($this->' . $field->getName() . ");";
        }
        $code[] = "return \$item;";
        $build->setCode($code);
        $build->addAnnotation('throws', '\InvalidArgumentException');
        $this->addMethod($build);
    }

}