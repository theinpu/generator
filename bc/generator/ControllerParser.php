<?php
/**
 * User: inpu
 * Date: 25.01.14
 * Time: 18:16
 */

namespace bc\generator;

use bc\generator\parser\Action;

class ControllerParser extends Parser {

    /**
     * @var Action[]
     */
    private $actions = array();
    private $commandClass = null;

    public function __construct($file) {
        parent::__construct($file);
        if (isset($this->data['actions'])) {
            foreach ($this->data['actions'] as $name => $info) {
                $this->actions[$name] = new Action($name, $info);
            }
        }
        if (isset($this->data['command'])) {
            $this->commandClass = $this->data['command'];
            $this->commandNameSpace = $this->getNamespace($this->commandClass);
        }
    }

    public function getActions() {
        return $this->actions;
    }

    public function getCommandClass() {
        if (is_null($this->commandClass)) throw new \RuntimeException();
        return str_replace($this->commandNameSpace . '\\', '', $this->commandClass);
    }

    public function getCommandNamespace() {
        if (is_null($this->commandNameSpace)) throw new \RuntimeException();
        return $this->commandNameSpace;
    }
}