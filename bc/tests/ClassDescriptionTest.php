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
        $class = new ClassDescription('Test', true);
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
        $class = new ClassDescription('MethodTest', true);
        $method = new Method('test', true);
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
        $class = new ClassDescription('PropertyTest', true);
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
            "\t".'private $field;',
            "\t".'public function getField() {',
            "\t\t".'return $this->field;',
            "\t}",
            "\t".'public function setField($field) {',
            "\t\t".'$this->field = $field;',
            "\t}",
            '}'
        );

        $this->assertEquals($code, $class->export());
    }

    public function testParent() {
        $class = new ClassDescription('ChildClass');
        $class->setParent('ParentClass');
        $this->assertContains(' extends ParentClass', $class->export(true));
        $class->setParent('AnotherParentClass');
        $this->assertContains(' extends AnotherParentClass', $class->export(true));
    }

    public function testInterfaces() {
        $class = new ClassDescription('Implement');
        $class->addInterface('IExportable');
        $class->addInterface('ArrayAccess');

        $this->assertContains(' implements IExportable, ArrayAccess', $class->export(true));
    }

    public function testAbstractClass() {
        $class = new ClassDescription('AbstractClass');
        $class->setAbstract(true);

        $code = array(
            'abstract class AbstractClass {',
            '}'
        );

        $this->assertEquals($code, $class->export());
    }

}
 