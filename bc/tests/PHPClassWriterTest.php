<?php
/**
 * User: anubis
 * Date: 27.01.14
 * Time: 18:43
 */

namespace bc\tests;


use bc\code\description\ClassDescription;
use bc\code\description\Method;
use bc\code\description\Parameter;
use bc\code\description\Property;
use bc\code\PHPClassWriter;
use bc\config\ConfigManager;

class PHPClassWriterTest extends \PHPUnit_Framework_TestCase {

    public function testPHPWriter() {
        $class = new ClassDescription('TestClass');
        $class->setNamespace('bc\test');

        $field = new Property('field');
        $class->addProperty($field);

        $method = new Method('__construct');
        $param = new Parameter('field');
        $method->addParameter($param);
        $method->appendCode('$this->field = $field;');
        $class->addMethod($method);

        $writer = new PHPClassWriter($class);

        $code = <<<CODE
<?php

namespace bc\\test;

class TestClass {

\tprivate \$field;

\tpublic function __construct(\$field) {
\t\t\$this->field = \$field;
\t}

}
CODE;

        $this->assertEquals($code, $writer->getCode());

        $file = ConfigManager::get('config/generator')->get('save.path').'test/TestClass.php';

        $this->assertTrue($writer->write($file));
        $this->assertEquals($code, file_get_contents($file));

        unlink($file);
        rmdir(dirname($file));
    }

}
 