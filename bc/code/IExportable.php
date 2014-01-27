<?php
/**
 * User: anubis
 * Date: 27.01.14
 * Time: 14:13
 */

namespace bc\code;

interface IExportable {

    /**
     * @param bool $asText
     * @return array|string
     */
    public function export($asText = false);

} 