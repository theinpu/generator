<?php
/**
 * User: inpu
 * Date: 24.10.13 13:48
 */

namespace bc\generator\struct;


class FieldDescription implements Exportable {

    /**
     * @var MethodDescription
     */
    protected $setterMethod;
    /**
     * @var MethodDescription
     */
    protected $getterMethod;

    /**
     * @var string
     */
    protected $name;
    protected $modifier = 'private';
    protected $useDoc = false;
    protected $useGetter = false;
    protected $useSetter = false;
    protected $getter;
    protected $setter;
    protected $isStatic = false;
    protected $type;

    /**
     * @param string $name
     * @param string $type
     */
    function __construct($name, $type = '') {
        $this->name = $name;
        $this->type = $type;
        $this->getter = 'get' . ucfirst($name);
        $this->setter = 'set' . ucfirst($name);
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    public function getModifier() {
        return $this->modifier;
    }

    /**
     * @return string
     */
    public function export() {
        $out = '';
        if($this->useDoc) {
            $doc = new PHPDocDescription();
            if(!empty($this->type)) {
                $doc->addAnnotation('var', $this->type);
            }
            $out .= $doc->export() . "\n";
        }
        $out .= $this->modifier;
        if($this->isStatic) {
            $out .= ' static';
        }
        $out .= ' $' . $this->name . ';';

        return $out;
    }

    /**
     * @param string $modifier
     */
    public function setModifier($modifier) {
        $this->modifier = $modifier;
    }

    public function useDoc() {
        $this->useDoc = true;
    }

    public function setUseGetter($name = '') {
        $this->useGetter = true;
        if(!empty($name)) $this->getter = $name;
    }

    public function setUseSetter($name = '') {
        $this->useSetter = true;
        if(!empty($name)) $this->setter = $name;
    }

    public function getter() {
        return $this->getter;
    }

    public function setter() {
        return $this->setter;
    }

    public function setStatic($isStatic) {
        $this->isStatic = $isStatic;
    }

    public function isUseGetter() {
        return $this->useGetter;
    }

    public function isUserSetter() {
        return $this->useSetter;
    }

    public function getSetter() {
        if(is_null($this->setterMethod)) {
            $this->setterMethod = new MethodDescription($this->setter);
            $param = new ParamDescription($this->name);
            $param->setType(is_null($this->type) ? 'mixed' : $this->type);
            $this->setterMethod->addParam($param);
            $this->setterMethod->setCode('$this->' . $this->name . ' = $' . $this->name . ';');
        }

        return $this->setterMethod;
    }

    /**
     * @return MethodDescription
     */
    public function getGetter() {
        if(is_null($this->getterMethod)) {
            $this->getterMethod = new MethodDescription($this->getter);
            $this->getterMethod->setType(is_null($this->type) ? 'mixed' : $this->type);
            $this->getterMethod->setCode('return $this->' . $this->name . ';');
        }

        return $this->getterMethod;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function getType() {
        return $this->type;
    }

}