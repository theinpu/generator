<?php
/**
 * User: anubis
 * Date: 27.01.14
 * Time: 14:55
 */

namespace bc\code\description;

class Method extends Description {

    private $modifier = Description::_PUBLIC;
    private $methodCode = array();

    /**
     * @var PHPDoc
     */
    private $doc;
    private $abstract = false;
    private $static = false;
    /**
     * @var Parameter[]
     */
    private $params = array();

    public function __construct($name = '') {
        parent::__construct($name);
        $this->doc = new PHPDoc();
    }

    /**
     * @param bool $asText
     * @return array|string
     */
    public function export($asText = false) {
        $this->cleanCode();
        $params = $this->prepareParams();

        parent::appendCode($this->doc->export());
        $types = $this->getTypesString();
        parent::appendCode($this->modifier.$types.' function '.$this->getName().'('. $params .') {');
        parent::appendCode($this->indent($this->methodCode));
        parent::appendCode('}');
        return parent::export($asText);
    }

    public function appendCode($code) {
        if(!is_array($code)) {
            $code = array($code);
        }
        $this->methodCode = array_merge($this->methodCode, $code);
    }

    /**
     * @param $description
     */
    public function setDescription($description) {
        $this->doc->setName($description);
    }

    /**
     * @return PHPDoc
     */
    public function getDoc() {
        return $this->doc;
    }

    /**
     * @param int $modifier
     */
    public function setModifier($modifier) {
        $this->modifier = $modifier;
    }

    public function setAbstract($abstract) {
        $this->abstract = $abstract;
    }

    public function setStatic($static) {
        $this->static = $static;
    }

    /**
     * @return string
     */
    private function getTypesString() {
        $types = '';
        if ($this->abstract) {
            $types .= ' abstract';
        }
        if ($this->static) {
            $types .= ' static';
            return $types;
        }
        return $types;
    }

    /**
     * @param Parameter $parameter
     */
    public function addParameter($parameter) {
        $this->params[] = $parameter;
    }

    private function prepareParams() {
        $params = array();
        if(count($this->params) > 0) {
            foreach($this->params as $param) {
                $type = $param->getType();
                if(!empty($type)) $type .= ' ';
                $typeHint = $param->typeHint() ? $type : '';

                $default = $param->hasDefault() ? ' = '.$param->getDefault() : '';

                $params[] = $typeHint.'$'.$param->getName().$default;
                $this->getDoc()->addAnnotation('var', $type.'$'.$param->getName());
            }
        }
        return implode(', ', $params);
    }

}