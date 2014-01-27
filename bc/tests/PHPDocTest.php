<?php
/**
 * User: anubis
 * Date: 27.01.14
 * Time: 14:19
 */

namespace bc\tests;

use bc\code\description\PHPDoc;

class PHPDocTest extends \PHPUnit_Framework_TestCase {

    public function testCreate() {
        $phpDoc = new PHPDoc('description');
        $phpDoc->addAnnotation('key', 'value');

        $doc = <<<PHPDOC
/**
 * description
 * @key value
 */
PHPDOC;

        $this->assertEquals($doc, $phpDoc->export(true));
    }

}
 