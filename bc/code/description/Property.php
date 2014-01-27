<?php
/**
 * User: anubis
 * Date: 27.01.14
 * Time: 16:22
 */

namespace bc\code\description;

class Property extends AccessibleDescription
{

    /**
     * @var Method
     */
    private $getter = null;

    public function __construct($name = '') {
        parent::__construct($name);
    }

    public function export($asText = false) {
        $this->cleanCode();
        $this->getDoc()->clearAnnotations();
        $type = $this->getType();
        if (!empty($type)) {
            $type = $type . ' ';
        }
        $default = '';
        if ($this->hasDefault()) {
            $default = ' = ' . $this->getDefault();
        }
        $this->getDoc()->addAnnotation('var', $type . '$' . $this->getName());
        $this->appendCode($this->getDoc()->export());
        $this->appendCode($this->getModifier() . ' $' . $this->getName() . $default . ';');
        return parent::export($asText);
    }

    /**
     * @param string $name
     * @return Method
     */
    public function getGetter($name = '') {
        if (is_null($this->getter)) {
            if (empty($name)) {
                $name = 'get' . ucfirst($this->getName());
            }
            $this->getter = new Method($name);
            $this->getter->setType($this->getType());
            $this->getter->appendCode('return $this->'.$this->getName().';');
        }
        return $this->getter;
    }

}