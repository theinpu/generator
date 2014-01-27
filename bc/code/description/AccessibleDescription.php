<?php
/**
 * User: anubis
 * Date: 27.01.14
 * Time: 16:36
 */

namespace bc\code\description;


class AccessibleDescription extends Description {

    private $modifier = Description::_PUBLIC;
    private $abstract = false;
    private $static = false;
    private $type = null;
    /**
     * @var mixed
     */
    private $default = null;

    /**
     * @var PHPDoc
     */
    private $doc;
    private $hasDefault = false;

    public function __construct($name = '') {
        parent::__construct($name);
        $this->doc = new PHPDoc();
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

    public function setType($type) {
        $this->type = $type;
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

    /**
     * @return string
     */
    public function getDefault() {
        $value = var_export($this->default, true);
        $value = str_replace("\n", '', $value);
        return $value;
    }

    /**
     * @return string
     */
    protected function getModifier() {
        return $this->modifier;
    }

    protected function isAbstract() {
        return $this->abstract;
    }

    protected function isStatic() {
        return $this->static;
    }

    protected function getType() {
        return $this->type;
    }

}