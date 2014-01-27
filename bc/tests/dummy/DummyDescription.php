<?php
/**
 * User: anubis
 * Date: 27.01.14
 * Time: 14:09
 */

namespace bc\tests\dummy;

use bc\code\description\Description;

class DummyDescription extends Description
{

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
}