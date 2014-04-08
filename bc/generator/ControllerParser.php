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
    private $commandNameSpace = null;
    private $routerClass = null;
    private $routerNamespace = null;
    private $baseUrl = '/';

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
        if(isset($this->data['router'])) {
            $this->routerClass = $this->data['router'];
            $this->routerNamespace = $this->getNamespace($this->routerClass);
        }
        if(isset($this->data['baseUrl'])) {
            $this->baseUrl = $this->data['baseUrl'];
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

    public function getRouterNamespace() {
        if(is_null($this->routerNamespace)) throw new \RuntimeException();
        return $this->routerNamespace;
    }

    public function getRouterClass() {
        if (is_null($this->routerClass)) throw new \RuntimeException();
        return str_replace($this->routerNamespace . '\\', '', $this->routerClass);
    }

    public function getBaseUrl() {
        if(is_null($this->baseUrl)) throw new \RuntimeException();
        return $this->baseUrl;
    }
}