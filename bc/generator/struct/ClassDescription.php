<?php
/**
 * User: inpu
 * Date: 24.10.13 13:49
 */

namespace bc\generator\struct;

class ClassDescription extends Description {

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
    private $usages = array();

    public function __construct($name, $namespace = '') {
        $this->namespace = $namespace;
        parent::__construct($name);
    }

    /**
     * @param $colorize
     * @return string
     */
    public function export($colorize = false) {
        parent::export($colorize);
        $out = '';
        $out .= $this->insertDoc();
        $out .= 'class ';
        $out .= $this->getName();
        if (!is_null($this->parent)) {
            $out .= ' extends ' . $this->parent;
        }
        if (count($this->interfaces) > 0) {
            $out .= ' implements ' . implode(', ', $this->interfaces);
        }
        $out .= " {\n";
        $out .= $this->indent($this->insertFields());
        $out .= $this->indent($this->insertMethods());

        $out .= "\n}\n";

        if ($colorize) {
            $out .= '<fg=yellow>' . $out . '</fg=yellow>';
        }

        return $out;
    }

    protected function insertDoc() {
        $out = '';
        if ($this->useDoc) {
            if (is_null($this->getDoc())) {
                $doc = new PHPDocDescription();
                $this->getDoc()->setDescription('Class ' . $this->getName());
                if (!empty($this->namespace)) {
                    $doc->addAnnotation('package', $this->namespace);
                }
            } else {
                $doc = $this->getDoc();
                $doc->setDescription('Class ' . $this->getName());
            }
            $out .= $doc->export(false) . "\n";
        }

        return $out;
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
                $out .= "\n" . $field->export(false);
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
        $out = array();
        if (count($this->methods) > 0) {
            $out[] = "";
            foreach ($this->methods as $method) {
                if ($this->useDoc) {
                    $method->useDoc();
                }
                $out[] = $method->export(false);
            }

            return implode("", $out);
        }

        return '';
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
        return ltrim($this->namespace, '\\') . '\\' . $this->getName();
    }

    /**
     * @return array
     */
    public function getUsages() {
        return $this->usages;
    }

    public function addUsage($usage) {
        $this->usages[] = $usage;
    }

}