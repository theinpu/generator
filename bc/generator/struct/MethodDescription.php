<?php
/**
 * User: inpu
 * Date: 24.10.13 13:48
 */

namespace bc\generator\struct;


class MethodDescription implements Exportable {

    private $name;
    private $modifier = 'public';
    /**
     * @var ParamDescription[]
     */
    private $params = array();
    private $code = '';
    private $useDoc = false;
    private $description;
    private $type;
    private $isStatic = false;

    public function __construct($name) {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function export() {
        $out = '';
        $out .= $this->insertDoc();
        $out .= $this->modifier;
        if($this->isStatic) {
            $out .= ' static';
        }
        $out .= ' function ' . $this->name . '(';
        $out .= $this->insertParams();
        $out .= ') {';
        if(!empty($this->code)) {
            $out .= "\n" . $this->code . "\n";
        }
        $out .= '}';

        return $out;
    }

    /**
     * @param ParamDescription $param
     */
    public function addParam($param) {
        $this->params[] = $param;
    }

    public function setCode($code) {
        $this->code = $code;
    }

    public function useDoc() {
        $this->useDoc = true;
    }

    /**
     * @return string
     */
    private function insertParams() {
        $out = '';
        if(count($this->params) > 0) {
            $params = array();
            foreach($this->params as $param) {
                $params[] = $param->export();
            }
            $out .= implode(', ', $params);

            return $out;
        }

        return $out;
    }

    private function insertDoc() {
        $out = '';
        if($this->useDoc) {
            $doc = new PHPDocDescription();
            if(!is_null($this->description)) {
                $doc->setDescription($this->description);
            }
            if(count($this->params) > 0) {
                foreach($this->params as $param) {
                    $value = '';
                    if(!is_null($param->getType())) {
                        $value .= $param->getType() . ' ';
                    }
                    $value .= '$' . $param->getName();
                    if(!is_null($param->getDescription())) {
                        $value .= ' ' . $param->getDescription();
                    }
                    $doc->addAnnotation('param', $value);
                }
            }
            if(!is_null($this->type)) {
                $doc->addAnnotation('return', $this->type);
            }
            $out .= $doc->export() . "\n";
        }

        return $out;
    }

    /**
     * @param $description
     */
    public function setDescription($description) {
        $this->description = $description;
    }

    /**
     * @param $type
     */
    public function setType($type) {
        $this->type = $type;
    }

    public function setModifier($modifier) {
        $this->modifier = $modifier;
    }

    /**
     * @param bool $isStatic
     */
    public function setStatic($isStatic) {
        $this->isStatic = $isStatic;
    }

    public function appendCode($code) {
        $this->code .= $code;
    }
}