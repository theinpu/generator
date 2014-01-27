<?php
/**
 * User: anubis
 * Date: 27.01.14
 * Time: 14:55
 */

namespace bc\tests;

use bc\code\description\Description;
use bc\code\description\Method;

class MethodTest extends \PHPUnit_Framework_TestCase {


    public function testCreateDefault() {
        $method = new Method('testMethod');
        $method->appendCode("echo '123';");
        $method->setDescription('test method');

        $code = array(
            '/**',
            ' * test method',
            ' */',
            'public function testMethod() {',
            "\techo '123';",
            '}'
        );

        $this->assertEquals($code, $method->export());
    }

    public function testModifiers() {
        $method = new Method('modifiersTest');
        $result = $method->export();
        $this->assertContains('public', $result[0]);
        $method->setModifier(Description::_PRIVATE);
        $result = $method->export();
        $this->assertContains('private', $result[0]);
        $method->setModifier(Description::_PROTECTED);
        $result = $method->export();
        $this->assertContains('protected', $result[0]);
    }

}