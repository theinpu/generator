<?php
/**
 * User: anubis
 * Date: 27.01.14
 * Time: 14:55
 */

namespace bc\tests;

use bc\code\description\Description;
use bc\code\description\Method;
use bc\code\description\Parameter;

class MethodTest extends \PHPUnit_Framework_TestCase {


    public function testCreateDefault() {
        $method = new Method('testMethod');
        $method->appendCode("echo '123';");
        $method->setDescription('test method');
        $method->getDoc()->addAnnotation('return','void');

        $code = array(
            '/**',
            ' * test method',
            ' * @return void',
            ' */',
            'public function testMethod() {',
            "\techo '123';",
            '}'
        );

        $this->assertEquals($code, $method->export());
    }

    public function testModifiers() {
        $method = new Method('modifiersTest');
        $this->assertContains('public ', $method->export(true));
        $method->setModifier(Description::_PRIVATE);
        $this->assertContains('private ', $method->export(true));
        $method->setModifier(Description::_PROTECTED);
        $this->assertContains('protected ', $method->export(true));
    }

    public function testAbstract() {
        $method = new Method('abstractTest');
        $this->assertNotContains(' abstract ', $method->export(true));
        $method->setAbstract(true);
        $this->assertContains(' abstract ', $method->export(true));
    }

    public function testStatic() {
        $method = new Method('staticTest');
        $this->assertNotContains(' static ', $method->export(true));
        $method->setStatic(true);
        $this->assertContains(' static ', $method->export(true));
    }

    public function testAbstractStatic() {
        $method = new Method('abstractStatic');
        $this->assertNotContains(' abstract static ', $method->export(true));
        $method->setAbstract(true);
        $method->setStatic(true);
        $this->assertContains(' abstract static ', $method->export(true));
    }

    public function testParams() {
        $method = new Method('params');
        /** @var Parameter[] $params */
        $params = array();
        $params[0] = new Parameter('test');
        $params[0]->setType('Description', true);
        $method->addParameter($params[0]);
        $params[1] = new Parameter('def');
        $params[1]->setType('string');
        $params[1]->setDefault('testing');
        $method->addParameter($params[1]);

        $code = array(
            '/**',
            ' * @var Description $test',
            ' * @var string $def',
            ' */',
            'public function params(Description $test, $def = \'testing\') {',
            '}'
        );
        $this->assertEquals($code, $method->export());
    }

}