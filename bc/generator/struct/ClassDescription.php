<?php
/**
 * User: inpu
 * Date: 24.10.13 13:49
 */

namespace bc\generator\struct;

class ClassDescription extends Description {

    private $name;
    private $namespace;
    /**
     * @var FieldDescription[]
     */
    private $fields = array();
    /**
     * @var MethodDescription[]
     */
    private $methods = array();
    private $parent;
    private $interfaces = array();
    /**
     * @var PHPDocDescription
     */
    private $doc = null;

    public function __construct($name, $namespace = '') {
        $this->name = $name;
        $this->namespace = $namespace;
        parent::__construct($name);
    }

    /**
     * @return string
     */
    public function export() {
        $out = '';
        $out .= $this->insertDoc();
        $out .= 'class ';
        $out .= $this->name;
        if (!is_null($this->parent)) {
            $out .= ' extends ' . $this->parent;
        }
        if (count($this->interfaces) > 0) {
            $out .= ' implements ' . implode(', ', $this->interfaces);
        }
        $out .= ' {';
        $out .= $this->insertFields();
        $out .= $this->insertMethods();

        $out .= '}';

        return $out;
    }

    public function useDoc() {
        $this->useDoc = true;
    }

    public function setDoc(PHPDocDescription $doc) {
        $this->doc = $doc;
    }


    /**
     * @param FieldDescription $field
     */
    public function addField($field) {
        $this->fields[] = $field;
        if ($field->isUseGetter()) {
            $this->addMethod($field->getGetter());
        }
        if ($field->isUserSetter()) {
            $this->addMethod($field->getSetter());
        }
    }

    public function addMethod($method) {
        $this->methods[] = $method;
    }

    /**
     * @return string
     */
    private function insertFields() {
        $out = '';
        if (count($this->fields) > 0) {
            $out .= "\n\n//region Fields\n";
            foreach ($this->fields as $field) {
                if ($this->useDoc) {
                    $field->useDoc();
                }
                $out .= "\n" . $field->export();
            }
            $out .= "\n\n//endregion\n";

            return $out;
        }

        return $out;
    }

    public function getNamespace() {
        return $this->namespace;
    }

    /**
     * @return string
     */
    private function insertMethods() {
        $out = '';
        if (count($this->methods) > 0) {
            foreach ($this->methods as $method) {
                if ($this->useDoc) {
                    $method->useDoc();
                }
                $out .= "\n" . $method->export() . "\n";
            }

            return $out . "\n";
        }

        return $out;
    }

    public function setParent($parent) {
        $this->parent = $parent;
    }

    public function addInterface($interface) {
        $this->interfaces[] = $interface;
    }

    /**
     * @return FieldDescription[]
     */
    public function getFields() {
        return $this->fields;
    }

    public function getNameForUsage() {
        return ltrim($this->namespace, '\\') . '\\' . $this->name;
    }

}