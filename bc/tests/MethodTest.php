<?php
/**
 * User: anubis
 * Date: 27.01.14
 * Time: 14:55
 */

namespace bc\tests;


use bc\code\description\Method;

class MethodTest extends \PHPUnit_Framework_TestCase {


    public function testCreate() {
        $method = new Method('testMethod');
        $method->appendCode("echo '123';");

        $code = array(
            'public function testMethod() {',
            "\techo '123';",
            '}'
        );

        $this->assertEquals($code, $method->export());
    }

}
 