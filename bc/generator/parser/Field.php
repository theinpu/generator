<?php
/**
 * User: inpu
 * Date: 25.01.14
 * Time: 5:54
 */

namespace bc\generator\parser;


class Field {

    private $name;
    private $type;
    private $sqlType;
    private $flags = array();
    private $getter;
    private $setter;
    private $useChange = true;
    private $ref = null;
    private $default;

    public function __construct($name, $info) {
        $this->name = $name;
        $this->type = isset($info['type']) ? $info['type'] : null;
        $this->sqlType = isset($info['sqlType']) ? $info['sqlType'] : null;
        $this->flags = isset($info['flags']) ? $info['flags'] : array();
        if (isset($info['get'])) {
            $this->getter = isset($info['get']['name']) ? $info['get']['name'] : 'get' . ucfirst($this->name);
        } else {
            $this->getter = 'get' . ucfirst($this->name);
        }
        if (isset($info['set'])) {
            $this->setter = isset($info['set']['name']) ? $info['set']['name'] : 'set' . ucfirst($this->name);
            if (isset($info['set']['change'])) {
                $this->useChange = $info['set']['change'];
            }
        } else {
            $this->setter = 'set' . ucfirst($this->name);
        }
        if (isset($info['ref'])) {
            $this->ref = $info['ref'];
            if (strpos($this->ref, '(') === false) {
                $this->ref .= '()';
            }
        }
        $this->default = isset($info['default']) ? $info['default'] : null;
    }

    public function hasType() {
        return !is_null($this->type);
    }

    public function getType() {
        return $this->type;
    }

    public function isReadOnly() {
        return in_array('RO', $this->flags);
    }

    public function getter() {
        return $this->getter;
    }

    public function setter() {
        return $this->setter;
    }

    public function useChange() {
        return $this->useChange;
    }

    public function getSqlType() {
        return $this->sqlType;
    }

    public function isRef() {
        return !is_null($this->ref);
    }

    public function getRef() {
        return $this->ref;
    }

    public function getName() {
        return $this->name;
    }

    public function isRequired() {
        return $this->isReadOnly() && in_array('NN', $this->flags);
    }

    public function getDefault() {
        return $this->default;
    }

    public function getFlags() {
        return $this->flags;
    }

} 