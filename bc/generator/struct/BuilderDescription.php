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
        $namespace = $parser->getNamespace();
        $this->parser = $parser;
        $this->model = $model;
        $this->fullClassName = $this->parser->getNamespace() . '\\' . $this->parser->getClass();
        parent::__construct($name, $namespace);
        $this->addInterface('IBuilder');
        $this->useDoc();
        $this->insertFields();
        $this->insertCreate();
        $this->insertBuild();
    }

    private function insertFields() {
        foreach($this->parser->getFields() as $name => $info) {
            if($name == 'id') continue;
            $field = new FieldDescription($name, $info['type']);
            $field->setUseSetter($name);
            $code = "\nreturn \$this;";
            $field->getSetter()->setType($this->parser->getClass() . 'Builder');
            $field->getSetter()->appendCode($code);
            $this->addField($field);
        }
    }

    private function insertCreate() {
        $create = new MethodDescription('create');
        $create->setType($this->getName());
        $create->setStatic(true);
        $create->setCode('return new self();');
        $this->addMethod($create);
    }

    private function insertBuild() {
        $build = new MethodDescription('build');
        $build->setType($this->fullClassName);
        $code = '';
        /** @var ModelFieldDescription[] $fields */
        $fields = $this->model->getFields();
        foreach($fields as $field) {
            if($field->isRequired()) {
                $code .= 'if(is_null($this->'.$field->getName().')) {'."\n";
                $code .= "\tthrow new \\InvalidArgumentException('Need to set ".$field->getName()."');\n";
                $code .= "}\n";
            }
        }
        $code .= '$item = new ' . $this->parser->getClass() . "();\n";
        foreach($this->model->getFields() as $field) {
            if($field->getName() == 'id') continue;
            $code .= '$item->' . $field->setter() . '($this->' . $field->getName() . ");\n";
        }
        $code .= "return \$item;";
        $build->setCode($code);
        $this->addMethod($build);
    }

}