<?php
/**
 * User: inpu
 * Date: 24.10.13 13:59
 */

namespace bc\generator\struct;


interface Exportable {

    /**
     * @param $colorize
     * @return string
     */
    public function export($colorize);

    public function addAnnotation($name, $value);

} 