<?php
/**
 * User: anubis
 * Date: 27.01.14
 * Time: 18:43
 */

namespace bc\code;

class PHPClassWriter extends ClassWriter
{

    /**
     * @return string
     */
    public function getCode() {
        $code = array(
            '<?php',
            '',
        );

        if(!is_null($this->getClass()->getNamespace())) {
            $code[] = 'namespace '.$this->getClass()->getNamespace().';';
            $code[] = '';
        }

        $code = array_merge($code, $this->getClass()->export());

        return implode("\n", $code);
    }

}