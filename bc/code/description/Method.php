<?php
/**
 * User: anubis
 * Date: 27.01.14
 * Time: 14:55
 */

namespace bc\code\description;

class Method extends AccessibleDescription {

    private $methodCode = array();

    /**
     * @var Parameter[]
     */
    private $params = array();

    /**
     * @param bool $asText
     * @return array|string
     */
    public function export($asText = false) {
        $this->cleanCode();
        $this->getDoc()->clearAnnotations();
        $params = $this->prepareParams();
        $type = $this->getType();
        if(!empty($type)) {
            $this->getDoc()->addAnnotation('return', $type);
        }
        parent::appendCode($this->getDoc()->export());
        $types = $this->getTypesString();
        parent::appendCode($this->getModifier() .$types.' function '.$this->getName().'('. $params .') {');
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
     * @return string
     */
    private function getTypesString() {
        $types = '';
        if ($this->isAbstract()) {
            $types .= ' abstract';
        }
        if ($this->isStatic()) {
            $types .= ' static';
            return $types;
        }
        return $types;
    }

    /**
     * @param Parameter $parameter
     */
    public function addParameter(Parameter $parameter) {
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
                $this->getDoc()->addAnnotation('param', $type.'$'.$param->getName());
            }
        }
        return implode(', ', $params);
    }


}