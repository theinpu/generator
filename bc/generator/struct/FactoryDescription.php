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
        $namespace = $parser->getNamespace() . $parser->getPath('factory', true);
        $this->parser = $parser;
        $this->fullClassName = $this->parser->getNamespace() . '\\' . $this->parser->getClass();
        $this->setParent('Factory');
        parent::__construct($name, $parser , $namespace);
        $this->useDoc();
        if (!is_null($this->getNamespace())) {
            $this->getDoc()->addAnnotation('package', $this->getNamespace());
        }
        $this->getDoc()->addAnnotation('method', $this->parser->getClass() . ' get($id)');
        $this->getDoc()->addAnnotation('method', $this->parser->getClass() . '[] getAll()');
        $this->getDoc()->addAnnotation('method', $this->parser->getClass() . '[] getList($ids)');
        $this->getDoc()->addAnnotation('method', $this->parser->getClass() . '[] getPartial($offset, $count)');
    }
} 