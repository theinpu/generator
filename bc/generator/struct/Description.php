<?php
/**
 * User: inpu
 * Date: 25.01.14
 * Time: 6:57
 */

namespace bc\generator\struct;


class Description implements Exportable {
    protected $modifier = 'public';
    /**
     * @var ParamDescription[]
     */
    protected $params = array();
    protected $code = '';
    protected $useDoc = false;
    protected $description;
    protected $type;
    protected $isStatic = false;

    /**
     * @var PHPDocDescription
     */
    private $doc;
    private $name;

    public function __construct($name) {
        $this->name = $name;
        $this->doc = new PHPDocDescription();
    }

    /**
     * @return string
     */
    public function export() {
        return '';
    }

    public function addAnnotation($name, $value) {
        $this->doc->addAnnotation($name, $value);
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

    public function useDoc() {
        $this->useDoc = true;
    }

    protected function getDoc() {
        return $this->doc;
    }

    public function getName() {
        return $this->name;
    }

    protected function insertDoc() {
        $out = '';
        if ($this->useDoc) {
            $out .= $this->getDoc()->export() . "\n";
        }

        return $out;
    }

}