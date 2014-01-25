<?php
/**
 * User: inpu
 * Date: 24.10.13 13:48
 */

namespace bc\generator\struct;


class FieldDescription extends Description {

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
    protected $useGetter = false;
    protected $useSetter = false;
    protected $getter;
    protected $setter;

    /**
     * @param string $name
     * @param string $type
     */
    function __construct($name, $type = '') {
        parent::__construct($name);
        parent::setType($type);
        $this->getter = 'get' . ucfirst($name);
        $this->setter = 'set' . ucfirst($name);
    }


    public function getModifier() {
        return $this->modifier;
    }

    /**
     * @param $colorize
     * @return string
     */
    public function export($colorize) {
        $out = '';
        if($this->useDoc) {
            if(!empty($this->type)) {
                $this->getDoc()->addAnnotation('var', $this->type);
            }
            $out .= $this->getDoc()->export($colorize) . "\n";
        }
        $out .= $this->modifier;
        if($this->isStatic) {
            $out .= ' static';
        }
        $out .= ' $' . $this->getName() . ';';

        return $out;
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

    public function isUseGetter() {
        return $this->useGetter;
    }

    public function isUserSetter() {
        return $this->useSetter;
    }

    public function getSetter() {
        if(is_null($this->setterMethod)) {
            $this->setterMethod = new MethodDescription($this->setter);
            $param = new ParamDescription($this->getName());
            $param->setType(is_null($this->type) ? 'mixed' : $this->type);
            $this->setterMethod->addParam($param);
            $this->setterMethod->setCode('$this->' . $this->getName() . ' = $' . $this->getName() . ';');
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
            $this->getterMethod->setCode('return $this->' . $this->getName() . ';');
        }

        return $this->getterMethod;
    }

}