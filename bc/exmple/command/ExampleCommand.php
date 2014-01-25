<?php

namespace bc\exmple\command;

use bc\cmf\Command;

class ExampleCommand extends Command {

    public function __construct($method) {
        parent::__construct('\bc\example\controller\ExampleController', $method);
    }

    public function startPage() {
        return new self('startPage');
    }

    public function redirectExample() {
        return new self('redirectExample');
    }

}
