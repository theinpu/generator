<?php
/**
 * User: inpu
 * Date: 24.10.13 13:50
 */

namespace bc\generator\struct;


class ParamDescription extends Description {

    private $default;
    private $isRef = false;
    private $namespace;

    function __construct($name, $default = null, $isRef = false) {
        parent::__construct($name);
        $this->default = $default;
        $this->isRef = $isRef;
    }

    /**
     * @param $colorize
     * @return string
     */
    public function export($colorize) {
        $param = '';
        if ($this->isRef) {
            $param .= '&';
        }
        $param .= '$' . $this->getName();
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

    public function getDescription() {
        return $this->description;
    }

    public function getNameSpace() {
        return $this->namespace;
    }

    public function setNameSpace($namespace) {
        $this->namespace = $namespace;
    }

}