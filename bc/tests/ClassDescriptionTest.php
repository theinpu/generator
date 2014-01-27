<?php
/**
 * User: anubis
 * Date: 27.01.14
 * Time: 17:31
 */

namespace bc\tests;


use bc\code\description\ClassDescription;
use bc\code\description\Method;
use bc\code\description\Property;

class ClassDescriptionTest extends \PHPUnit_Framework_TestCase {

    public function testCreateDefault() {
        $class = new ClassDescription('Test');
        $this->assertInstanceOf('bc\\code\\description\\ClassDescription', $class);

        $code = array(
            '/**',
            ' * Class Test',
            ' */',
            'class Test {',
            '}'
        );

        $this->assertEquals($code, $class->export());
    }

    public function testWithMethod() {
        $class = new ClassDescription('MethodTest');
        $method = new Method('test');
        $method->setDescription('test method');
        $method->appendCode("echo '123';");
        $class->addMethod($method);

        $code = array(
            '/**',
            ' * Class MethodTest',
            ' */',
            'class MethodTest {',
            "\t/**",
            "\t * test method",
            "\t */",
            "\t".'public function test() {',
            "\t\t"."echo '123';",
            "\t}",
            '}'
        );

        $this->assertEquals($code, $class->export());
    }

    public function testWithProperty() {
        $class = new ClassDescription('PropertyTest');
        $property = new Property('field');
        $property->setType('string');
        $property->setUseGetter(true);
        $property->setUseSetter(true);
        $class->addProperty($property);

        $code = array(
            '/**',
            ' * Class PropertyTest',
            ' */',
            'class PropertyTest {',
            "\t/**",
            "\t * ".'@var string $field',
            "\t */",
            "\t".'private $field;',
            "\t/**",
            "\t * @return string",
            "\t */",
            "\t".'public function getField() {',
            "\t\t".'return $this->field;',
            "\t}",
            "\t/**",
            "\t * @param string ".'$field',
            "\t */",
            "\t".'public function setField($field) {',
            "\t\t".'$this->field = $field;',
            "\t}",
            '}'
        );

        $this->assertEquals($code, $class->export());
    }

}
 