<?php
/**
 * User: anubis
 * Date: 27.01.14
 * Time: 15:50
 */

namespace bc\code\description;

class Parameter
{

    private $name;
    private $type = '';
    private $useTypeHint = false;
    /**
     * @var mixed
     */
    private $default = null;
    private $hasDefault = false;

    public function __construct($name) {
        $this->name = $name;
    }

    public function setType($type, $useTypeHint = false) {
        $this->type = $type;
        $this->useTypeHint = $useTypeHint;
    }

    public function getName() {
        return $this->name;
    }

    public function typeHint() {
        return $this->useTypeHint;
    }

    public function getType() {
        return $this->type;
    }

    /**
     * @param mixed $default
     */
    public function setDefault($default) {
        $this->default = $default;
        $this->hasDefault = true;
    }

    public function hasDefault() {
        return $this->hasDefault;
    }

    public function getDefault() {
        $value = var_export($this->default, true);
        $value = str_replace("\n", '', $value);
        return $value;
    }

}