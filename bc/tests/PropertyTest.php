<?php
/**
 * User: anubis
 * Date: 27.01.14
 * Time: 16:22
 */

namespace bc\tests;


use bc\code\description\Description;
use bc\code\description\Property;

class PropertyTest extends \PHPUnit_Framework_TestCase {

    public function testCreate() {
        $property = new Property('test', true);
        $property->setModifier(Description::_PRIVATE);
        $property->setType('int');
        $property->setDefault(0);
        $code = array(
            '/**',
            ' * @var int $test',
            ' */',
            'private $test = 0;'
        );
        $this->assertEquals($code, $property->export());
    }

    public function testGetter() {
        $property = new Property('withGetter', true);
        $property->setModifier(Description::_PRIVATE);
        $property->setType('string');
        $getter = $property->getGetter();
        $this->assertInstanceOf('bc\\code\\description\\Method', $getter);
        $getterCode = array(
            '/**',
            ' * @return string',
            ' */',
            'public function getWithGetter() {',
            "\t".'return $this->withGetter;',
            '}'
        );

        $this->assertEquals($getterCode, $getter->export());
    }

    public function testSetter() {
        $property = new Property('withSetter', true);
        $property->setModifier(Description::_PRIVATE);
        $property->setType('string');
        $setter = $property->getSetter();
        $this->assertInstanceOf('bc\\code\\description\\Method', $setter);
        $getterCode = array(
            '/**',
            ' * @param string $withSetter',
            ' */',
            'public function setWithSetter($withSetter) {',
            "\t".'$this->withSetter = $withSetter;',
            '}'
        );

        $this->assertEquals($getterCode, $setter->export());
    }

    public function testStatic() {
        $property = new Property('testStatic');
        $property->setStatic(true);

        $this->assertEquals(array('private static $testStatic;'), $property->export());
    }

}
 