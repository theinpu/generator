<?php
/**
 * User: anubis
 * Date: 27.01.14
 * Time: 14:09
 */

namespace bc\code\description;


class PHPDoc extends Description
{

    private $annotations;

    /**
     * @param bool $asText
     * @return array|string
     */
    public function export($asText = false) {
        $this->cleanCode();
        $description = $this->getName();
        if (!empty($description) || count($this->annotations) > 0) {
            $this->appendCode("/**");
            if (!empty($description)) {
                $this->appendCode(' * ' . $description);
            }
            if (count($this->annotations) > 0) {
                foreach ($this->annotations as $item) {
                    $this->appendCode(' * @' . $item['key'] . ' ' . $item['value']);
                }
            }
            $this->appendCode(" */");
        }
        return parent::export($asText);
    }

    public function addAnnotation($key, $value) {
        $this->annotations[] = array('key' => $key, 'value' => $value);
    }

    public function clearAnnotations() {
        $this->annotations = array();
    }

}