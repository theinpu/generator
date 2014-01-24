<?php
/**
 * User: anubis
 * Date: 24.01.14
 * Time: 17:11
 */

namespace bc\generator;

use Symfony\Component\Console\Application;

class GeneratorCLI extends Application {

    public function __construct() {
        parent::__construct('Model generator');

        $this->add(new GeneratorCommand());
    }
}