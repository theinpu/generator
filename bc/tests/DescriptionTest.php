<?php
/**
 * User: anubis
 * Date: 27.01.14
 * Time: 14:10
 */

namespace bc\tests;


use bc\tests\dummy\DummyDescription;

class DescriptionTest extends \PHPUnit_Framework_TestCase {

    public function testCreate() {
        $descr = new DummyDescription('test');
        $this->assertInstanceOf('bc\\code\\description\\Description', $descr);
        $this->assertInstanceOf('bc\\code\\Exportable', $descr);
        $this->assertEquals('test', $descr->getName());
    }

    public function testExport() {
        $descr = new DummyDescription('test');
        $this->assertEquals('test', $descr->export(true));
        $this->assertEquals(array('test'), $descr->export(false));
    }

}