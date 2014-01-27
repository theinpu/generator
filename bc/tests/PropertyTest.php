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
        $property = new Property('test');
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
}
 