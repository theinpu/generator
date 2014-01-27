<?php
/**
 * User: anubis
 * Date: 27.01.14
 * Time: 14:55
 */

namespace bc\code\description;

class Method extends Description {

    private $modifier = Description::_PUBLIC;
    private $methodCode = array();

    /**
     * @param bool $asText
     * @return array|string
     */
    public function export($asText = false) {
        parent::appendCode($this->modifier.' function '.$this->getName().'() {');
        parent::appendCode($this->indent($this->methodCode));
        parent::appendCode('}');
        return parent::export($asText);
    }

    public function appendCode($code) {
        if(!is_array($code)) {
            $code = array($code);
        }
        $this->methodCode = array_merge($this->methodCode, $code);
    }

    protected function indent($code) {
        if(!is_array($code)) {
            $lines = implode("\n", $code);
        } else {
            $lines = $code;
        }
        foreach($lines as &$line) {
            $line = "\t".$line;
        }
        if(!is_array($code)) {
            return implode("\n", $lines);
        } else {
            return $lines;
        }
    }

}