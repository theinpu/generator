<?php
/**
 * User: anubis
 * Date: 27.01.14
 * Time: 17:31
 */

namespace bc\code\description;

class ClassDescription extends AccessibleDescription {

    /**
     * @var Method[]
     */
    private $methods = array();
    /**
     * @var Property[]
     */
    private $properties = array();

    public function __construct($name = '') {
        parent::__construct($name);
        $this->getDoc()->setName('Class '.$name);
    }

    public function export($asText = false) {
        $this->cleanCode();

        $this->appendCode($this->getDoc()->export());
        $this->appendCode('class '.$this->getName().' {');
        if(count($this->properties) > 0) {
            foreach($this->properties as $property) {
                $this->appendCode($this->indent($property->export()));
                if($property->useGetter()) {
                    $this->appendCode($this->indent($property->getGetter()->export()));
                }
                if($property->useSetter()) {
                    $this->appendCode($this->indent($property->getSetter()->export()));
                }
            }
        }
        if(count($this->methods) > 0) {
            foreach($this->methods as $method) {
                $this->appendCode($this->indent($method->export()));
            }
        }
        $this->appendCode('}');

        return parent::export($asText);
    }

    /**
     * @param Method $method
     */
    public function addMethod(Method $method) {
        $this->methods[$method->getName()] = $method;
    }

    /**
     * @param Property $property
     */
    public function addProperty(Property $property) {
        $this->properties[$property->getName()] = $property;
    }

} 