<?php
/**
 * User: anubis
 * Date: 27.01.14
 * Time: 17:31
 */

namespace bc\code\description;

class ClassDescription extends AccessibleDescription
{

    /**
     * @var Method[]
     */
    private $methods = array();
    /**
     * @var Property[]
     */
    private $properties = array();
    private $parentClass = '';
    private $interfaces = array();
    private $namespace = null;

    public function __construct($name = '', $useDoc = false) {
        parent::__construct($name, $useDoc);
        $this->getDoc()->setName('Class ' . $name);
    }

    public function export($asText = false) {
        $this->cleanCode();

        if ($this->useDoc()) {
            $this->appendCode($this->getDoc()->export());
        }

        $extends = '';
        if (!empty($this->parentClass)) {
            $extends = ' extends ' . $this->parentClass;
        }
        $implements = '';
        if (count($this->interfaces) > 0) {
            $implements = ' implements ' . implode(', ', $this->interfaces);
        }
        $type = '';
        if($this->isAbstract()) {
            $type = 'abstract ';
        }

        $this->appendCode($type.'class ' . $this->getName() . $extends . $implements . ' {');
        if (count($this->properties) > 0) {
            $this->appendCode('');
            foreach ($this->properties as $property) {
                $this->appendCode($this->indent($property->export()));
                if ($property->useGetter()) {
                    $this->appendCode('');
                    $this->appendCode($this->indent($property->getGetter()->export()));
                }
                if ($property->useSetter()) {
                    $this->appendCode('');
                    $this->appendCode($this->indent($property->getSetter()->export()));
                }
            }
        }
        if (count($this->methods) > 0) {
            $this->appendCode('');
            foreach ($this->methods as $method) {
                $this->appendCode($this->indent($method->export()));
            }
            $this->appendCode('');
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

    public function setParent($parentClass) {
        $this->parentClass = $parentClass;
    }

    public function addInterface($interface) {
        $this->interfaces[] = $interface;
    }

    public function setNamespace($namespace) {
        $this->namespace = $namespace;
    }

    public function getNamespace() {
        return $this->namespace;
    }

} 