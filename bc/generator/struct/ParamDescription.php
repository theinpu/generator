<?php
/**
 * User: inpu
 * Date: 24.10.13 13:50
 */

namespace bc\generator\struct;


class ParamDescription implements Exportable {

    private $name;
    private $default;
    private $isRef = false;
    private $type = null;
    private $description;

    function __construct($name, $default = null, $isRef = false) {
        $this->name = $name;
        $this->default = $default;
        $this->isRef = $isRef;
    }

    /**
     * @return string
     */
    public function export() {
        $param = '';
        if ($this->isRef) {
            $param .= '&';
        }
        $param .= '$' . $this->name;
        if (!is_null($this->default)) {
            $param .= ' = ' . (is_string($this->default) ? "'{$this->default}'" : $this->default);
        }

        return $param;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function getType() {
        return $this->type;
    }

    public function getName() {
        return $this->name;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function addAnnotation($name, $value) {
        // TODO: Implement addAnnotation() method.
    }

}