<?php
/**
 * User: inpu
 * Date: 24.10.13 13:49
 */

namespace bc\generator\struct;


class PHPDocDescription implements Exportable {

    private $description = '';
    private $annotations = array();

    /**
     * @param $colorize
     * @return string
     */
    public function export($colorize) {
        $show = !empty($this->description) || !empty($this->annotations);
        $out = '';
        if ($show) {
            $out = '/**';
            $out = $this->insertDescription($out);
            $out = $this->insertAnnotations($out);
            $out .= "\n */";
        }
        return $out;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function addAnnotation($name, $value = '') {
        $this->annotations[] = array(
            'name' => $name,
            'value' => $value
        );
    }

    /**
     * @param $out
     *
     * @return string
     */
    private function insertAnnotations($out) {
        if (count($this->annotations) > 0) {
            foreach ($this->annotations as $annotation) {
                $out .= "\n * @" . $annotation['name'] . " " . $annotation['value'];
            }

            return $out;
        }

        return $out;
    }

    /**
     * @param $out
     *
     * @return string
     */
    private function insertDescription($out) {
        if (!empty($this->description)) {
            $out .= "\n * " . $this->description;

            return $out;
        }

        return $out;
    }
}