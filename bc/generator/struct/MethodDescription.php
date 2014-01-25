<?php
/**
 * User: inpu
 * Date: 24.10.13 13:48
 */

namespace bc\generator\struct;


class MethodDescription extends Description {


    /**
     * @param $colorize
     * @return string
     */
    public function export($colorize) {
        $out = "\n";
        $out .= $this->insertDoc();
        $out .= $this->modifier;
        if ($this->isStatic) {
            $out .= ' static';
        }
        $out .= ' function ' . $this->getName() . '(';
        $out .= $this->insertParams();
        $out .= ') {';
        if (!empty($this->code)) {
            $out .= "\n" . $this->indent($this->code) . "\n";
        }
        $out .= '}';

        return $out . "\n";
    }

    /**
     * @param ParamDescription $param
     */
    public function addParam($param) {
        $this->params[] = $param;
    }

    public function setCode($code) {
        if (!is_array($code)) {
            $code = array($code);
        }
        $this->code = $code;
    }

    /**
     * @return string
     */
    private function insertParams() {
        $out = '';
        if (count($this->params) > 0) {
            $params = array();
            foreach ($this->params as $param) {
                $params[] = $param->export(false);
            }
            $out .= implode(', ', $params);

            return $out;
        }

        return $out;
    }

    protected function insertDoc() {
        $out = '';
        if ($this->useDoc) {
            if (!is_null($this->description)) {
                $this->getDoc()->setDescription($this->description);
            }
            if (count($this->params) > 0) {
                foreach ($this->params as $param) {
                    $value = '';
                    if (!is_null($param->getType())) {
                        $value .= $param->getType() . ' ';
                    }
                    $value .= '$' . $param->getName();
                    if (!is_null($param->getDescription())) {
                        $value .= ' ' . $param->getDescription();
                    }
                    if (!is_null($param->getNameSpace())) {
                        $value = str_replace($param->getNameSpace() . '\\', '', $value);
                    }
                    $this->getDoc()->addAnnotation('param', $value);
                }
            }
            if (!is_null($this->type)) {
                $this->getDoc()->addAnnotation('return', $this->type);
            }
            $out .= $this->getDoc()->export(false) . "\n";
        }

        return $out;
    }

}