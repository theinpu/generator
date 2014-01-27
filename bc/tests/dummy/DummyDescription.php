<?php
/**
 * User: anubis
 * Date: 27.01.14
 * Time: 14:09
 */

namespace bc\tests\dummy;

use bc\code\description\Description;
use bc\code\description\PHPDoc;

class DummyDescription extends Description
{
    /**
     * @var PHPDoc
     */
    private $doc;

    /**
     * @param bool $asText
     * @return array|string
     */
    public function export($asText = false) {
        if ($asText) {
            return $this->getName();
        } else {
            return array($this->getName());
        }
    }

    /**
     * @param $description
     */
    public function setDescription($description) {
        $this->doc->setName($description);
    }

    /**
     * @return PHPDoc
     */
    public function getDoc() {
        return $this->doc;
    }
}