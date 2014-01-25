<?php
/**
 * User: inpu
 * Date: 23.10.13 14:53
 */

namespace bc\generator;


use bc\config\ConfigManager;
use bc\generator\parser\Field;

class Parser {
    private $savePath;

    /**
     * @var array
     */
    protected $data = array();
    /**
     * @var Field[]
     */
    private $fields = array();

    public function __construct($file) {
        $this->savePath = ConfigManager::get('config/generator')->get('save.path');
        if (!file_exists($file)) {
            throw new \RuntimeException(sprintf("File %s not found", $file));
        }
        $parser = new \Symfony\Component\Yaml\Parser();
        $this->data = $parser->parse(file_get_contents($file));
        $this->parseFields();
    }

    public function getRaw() {
        return $this->data;
    }

    /**
     * @param string $componentName
     *
     * @param bool $return
     * @return null
     */
    public function getPath($componentName, $return = false) {
        if (!isset($this->data['paths'])) {
            if ($return) {
                return '';
            }
            return $this->savePath . $this->generatePath();
        }
        if (!isset($this->data['paths'][$componentName])) {
            $path = $componentName;
        } else {
            $path = $this->data['paths'][$componentName];
        }
        if (strpos($path, '%same%') !== false) {
            $path = str_replace('%same%', $this->data['paths']['base'], $path);
        }

        if (!$return) {
            $path = $this->savePath . $this->generatePath() . '/' . $path;
        } elseif (!empty($path)) {
            $path = '\\' . $path;
        }

        return $path;
    }

    public function getNamespace() {
        $name = explode('\\', $this->getFullClass());
        array_pop($name);
        $name = implode('\\', $name);

        return $name;
    }

    private function getFullClass() {
        return $this->data['class'];
    }

    public function getClass() {
        return str_replace($this->getNamespace() . '\\', '', $this->getFullClass());
    }

    /**
     * @return parser\Field[]
     */
    public function getFields() {
        return $this->fields;
    }

    public function getParent() {
        if (!isset($this->data['parent'])) {
            return null;
        }

        return $this->data['parent'];
    }

    public function getTable() {
        return $this->data['table'];
    }

    public function getParentDescription() {
        return isset($this->data['parentDescription']) ? $this->data['parentDescription'] : null;
    }

    public function checkFlag($field, $flag) {
        if (!isset($this->data['fields'])) {
            throw new \RuntimeException("No fields found");
        }
        if (!isset($this->data['fields'][$field])) {
            throw new \RuntimeException("Field '{$field}' not found");
        }
        if (!isset($this->data['fields'][$field]['flags'])) {
            return false;
        }

        return in_array($flag, $this->data['fields'][$field]['flags']);
    }

    private function parseFields() {
        if (!isset($this->data['fields'])) return;
        foreach ($this->data['fields'] as $field => $info) {
            $this->fields[$field] = new Field($field, $info);
        }
    }

    private function generatePath() {
        $path = trim($this->getNamespace(), '\\');
        $path = str_replace('\\', DIRECTORY_SEPARATOR, $path);
        return $path;
    }
}