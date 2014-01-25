<?php
/**
 * User: inpu
 * Date: 24.10.13 18:35
 */

namespace bc\generator;

use bc\generator\struct\ClassDescription;

class ClassWriter {

    private $file;
    /**
     * @var ClassDescription
     */
    private $class;
    private $usages = array();

    /**
     * @param ClassDescription $class
     * @param $file
     */
    function __construct($class, $file) {
        $this->class = $class;
        $this->file = $file;
    }

    public function write() {
        $out = "<?php \n\n";
        $namespace = ltrim($this->class->getNamespace(), '\\');
        if(!empty($namespace)) {
            $out .= 'namespace ' . $namespace . ";\n\n";
        }
        if ($namespace != 'bc\\model' && strpos($this->class->getName(), 'Factory') !== false) {
            $this->addUsage('bc\\model\\Factory');
        }
        $out .= $this->insertUsages();
        $out .= $this->class->export();

        $dir = dirname($this->file);
        if(!file_exists($dir)) {
            mkdir($dir, 0775, true);
        }

        return file_put_contents($this->file, $out);
    }

    public function addUsage($usage) {
        $this->usages[] = $usage;
    }

    /**
     * @return string
     */
    private function insertUsages() {
        $out = '';
        if(count($this->usages) > 0) {
            $usages = array();
            foreach($this->usages as $usage) {
                $usages[] = 'use ' . ltrim($usage, '\\') . ";";
            }
            $out .= implode("\n", $usages) . "\n\n";

            return $out;
        }

        return $out;
    }

} 