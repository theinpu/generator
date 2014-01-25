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

    public function __construct($file) {
        parent::__construct($file);
        if (isset($this->data['actions'])) {
            foreach ($this->data['actions'] as $name => $info) {
                $this->actions[$name] = new Action($name, $info);
            }
        }
    }

    public function getActions() {
        return $this->actions;
    }
}