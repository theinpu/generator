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
    /**
     * @var Method
     */
    private $setter = null;
    private $useGetter = false;
    private $useSetter = false;

    public function __construct($name = '') {
        parent::__construct($name);
        $this->setModifier(Description::_PRIVATE);
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
        if (!empty($type)) {
            $this->appendCode($this->getDoc()->export());
        }
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
            $this->getter->appendCode('return $this->' . $this->getName() . ';');
        }
        return $this->getter;
    }

    /**
     * @return Method
     */
    public function getSetter() {
        if (is_null($this->setter)) {
            if (empty($name)) {
                $name = 'set' . ucfirst($this->getName());
            }
            $this->setter = new Method($name);
            $param = new Parameter($this->getName());
            $param->setType($this->getType());
            $this->setter->addParameter($param);
            $this->setter->appendCode('$this->' . $this->getName() . ' = $' . $this->getName() . ';');
        }
        return $this->setter;
    }

    public function useGetter() {
        return $this->useGetter;
    }

    public function useSetter(){
        return $this->useSetter;
    }

    public function setUseGetter($use) {
        $this->useGetter = $use;
    }

    public function setUseSetter($use) {
        $this->useSetter = $use;
    }

}