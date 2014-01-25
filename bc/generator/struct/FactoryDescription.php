<?php
/**
 * User: inpu
 * Date: 25.10.13 15:52
 */

namespace bc\generator\struct;

use bc\generator\Parser;

class FactoryDescription extends ClassDescription {

    private $fullClassName;

    /**
     * @var Parser
     */
    private $parser;

    /**
     * @param Parser $parser
     */
    public function __construct($parser) {
        $name = $parser->getClass() . 'Factory';
        $namespace = $parser->getNamespace();
        $this->parser = $parser;
        $this->fullClassName = $this->parser->getNamespace() . '\\' . $this->parser->getClass();
        $this->setParent('Factory');
        parent::__construct($name, $namespace);
        $this->useDoc();
        $doc = new PHPDocDescription();
        if (!is_null($this->getNamespace())) {
            $doc->addAnnotation('package', $this->getNamespace());
        }
        $doc->addAnnotation('method ', $this->parser->getClass().' get($id)');
        $doc->addAnnotation('method ', $this->parser->getClass().'[] getAll()');
        $doc->addAnnotation('method ', $this->parser->getClass().'[] getList($ids)');
        $doc->addAnnotation('method ', $this->parser->getClass().'[] getPartial($offset, $count)');
        $this->setDoc($doc);
    }
} 