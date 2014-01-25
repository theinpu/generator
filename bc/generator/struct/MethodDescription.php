<?php
/**
 * User: inpu
 * Date: 24.10.13 13:48
 */

namespace bc\generator\struct;


class MethodDescription extends Description {


    /**
     * @return string
     */
    public function export() {
        $out = '';
        $out .= $this->insertDoc();
        $out .= $this->modifier;
        if($this->isStatic) {
            $out .= ' static';
        }
        $out .= ' function ' . $this->getName() . '(';
        $out .= $this->insertParams();
        $out .= ') {';
        if(!empty($this->code)) {
            $out .= "\n" . $this->code . "\n";
        }
        $out .= '}';

        return $out;
    }

    /**
     * @param ParamDescription $param
     */
    public function addParam($param) {
        $this->params[] = $param;
    }

    public function setCode($code) {
        $this->code = $code;
    }

    public function useDoc() {
        $this->useDoc = true;
    }

    /**
     * @return string
     */
    private function insertParams() {
        $out = '';
        if(count($this->params) > 0) {
            $params = array();
            foreach($this->params as $param) {
                $params[] = $param->export();
            }
            $out .= implode(', ', $params);

            return $out;
        }

        return $out;
    }


}