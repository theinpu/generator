<?php
/**
 * User: anubis
 * Date: 27.01.14
 * Time: 18:40
 */

namespace bc\code;

use bc\code\description\ClassDescription;

abstract class ClassWriter {

    /**
     * @var ClassDescription
     */
    private $class = null;

    public function __construct(ClassDescription $class) {
        $this->class = $class;
    }

    /**
     * @return string
     */
    public abstract function getCode();

    /**
     * Write code to file
     * @param string $file
     * @return bool
     */
    public function write($file) {
        $dir = dirname($file);
        if(!file_exists($dir)) {
            mkdir($dir, 0775, true);
        }
        return (file_put_contents($file, $this->getCode()) > 0);
    }

    /**
     * @return ClassDescription
     */
    protected function getClass() {
        return $this->class;
    }

} 