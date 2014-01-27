<?php
/**
 * User: anubis
 * Date: 27.01.14
 * Time: 14:08
 */

namespace bc\code\description;


use bc\code\Exportable;

abstract class Description implements Exportable
{

    const _PUBLIC = 'public';
    const _PRIVATE = 'private';
    const _PROTECTED = 'protected';

    private $code = array();
    private $name = null;

    public function __construct($name = '') {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function appendCode($code) {
        if (!is_array($code)) {
            $code = array($code);
        }
        $this->code = array_merge($this->code, $code);
    }

    protected function getCode() {
        return $this->code;
    }

    /**
     * @param bool $asText
     * @return array|string
     */
    public function export($asText = false) {
        return $asText ? implode("\n", $this->getCode()) : $this->getCode();
    }

    public function setName($name) {
        $this->name = $name;
    }

    protected function cleanCode() {
        $this->code = array();
    }

    protected final function indent($code) {
        if (!is_array($code)) {
            $lines = implode("\n", $code);
        } else {
            $lines = $code;
        }
        foreach ($lines as &$line) {
            $line = "\t" . $line;
        }
        if (!is_array($code)) {
            return implode("\n", $lines);
        } else {
            return $lines;
        }
    }

} 