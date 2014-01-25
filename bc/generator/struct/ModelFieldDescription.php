<?php
/**
 * User: inpu
 * Date: 24.10.13 17:57
 */

namespace bc\generator\struct;

class ModelFieldDescription extends FieldDescription {

    private $useChanged = true;
    private $readOnly = false;
    private $required = false;

    public function getSetter() {
        if (is_null($this->setterMethod)) {
            $this->setterMethod = new MethodDescription($this->setter);
            $param = new ParamDescription($this->getName());
            $param->setType(is_null($this->type) ? 'mixed' : $this->type);
            $this->setterMethod->addParam($param);
            $code = $this->setterCode();
            $this->setterMethod->setCode($code);
        }

        return $this->setterMethod;
    }

    public function setUseChanged($change) {
        $this->useChanged = $change;
    }

    public function setReadOnly() {
        $this->readOnly = true;
    }

    public function setRequired() {
        $this->required = true;
    }

    /**
     * @return string
     */
    private function setterCode() {
        $code = '';
        if ($this->readOnly) {
            $this->setterMethod->addAnnotation('throws', '\RuntimeException');
            $code .= "if(!is_null(\$this->{$this->getName()}) && !is_null(\$this->getId())) throw new \\RuntimeException('Changing not allowed');\n";
        }
        $code .= '$this->' . $this->getName() . ' = $' . $this->getName() . ';';
        if ($this->useChanged) {
            $code .= "\n\$this->changed();";

            return $code;
        }

        return $code;
    }

    public function isRequired() {
        return $this->required;
    }

} 