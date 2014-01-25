<?php
/**
 * User: inpu
 * Date: 25.10.13 12:20
 */

namespace bc\generator\struct;


class TableDescription implements Exportable {

    private $table;
    private $columns = array();
    private $engine = 'InnoDB';
    private $encoding = 'utf8';
    private $primaryKeys = array();

    public function __construct($table) {
        $this->table = $table;
    }

    /**
     * @throws \InvalidArgumentException
     * @return string
     */
    public function export() {
        if (count($this->columns) == 0) {
            throw new \InvalidArgumentException('Need to set one or more columns');
        }
        $out = 'CREATE TABLE `' . $this->table . "` (\n";
        $out .= $this->insertColumns();
        $out .= $this->insertPrimaryKeys();
        $out .= ")\n";
        $out .= '  ENGINE =' . $this->engine . "\n";
        $out .= '  DEFAULT CHARSET =' . $this->encoding . ';';

        return $out;
    }

    public function addColumn($name, $type, $flags, $default = null) {
        $this->columns[$name] = array(
            'type' => $type,
            'flags' => $flags,
            'default' => $default
        );
        if ($this->checkFlag('PK', $this->columns[$name])) {
            $this->addPrimaryKey($name);
        }
    }

    /**
     * @param $flag
     * @param $info
     *
     * @return bool
     */
    private function checkFlag($flag, $info) {
        return array_search($flag, $info['flags']) !== false;
    }

    /**
     * @param $info
     *
     * @return mixed
     */
    private function escapeDefault($info) {
        if (strtoupper($info['default']) == 'NULL') {
            return $info['default'];
        }
        if (strpos($info['type'], 'VARCHAR') !== false || strpos($info['type'], 'TEXT') !== false) {
            return "'{$info['default']}'";
        }

        return $info['default'];
    }

    private function addPrimaryKey($name) {
        $this->primaryKeys[] = "`{$name}`";
    }

    /**
     * @return string
     */
    private function insertColumns() {
        $columns = '';
        foreach ($this->columns as $column => $info) {
            $columns .= "  `{$column}` " . $info['type'];
            if ($this->checkFlag('NN', $info)) {
                $columns .= ' NOT NULL';
            }
            if ($this->checkFlag('AI', $info)) {
                $columns .= ' AUTO_INCREMENT';
            }
            if (!is_null($info['default'])) {
                $columns .= ' DEFAULT ' . $this->escapeDefault($info);
            }
            $columns .= ",\n";
        }

        return $columns;
    }

    /**
     * @return string
     */
    private function insertPrimaryKeys() {
        $pk = '';
        if (count($this->primaryKeys) > 0) {
            $pk = '  PRIMARY KEY (' . implode(', ', $this->primaryKeys) . ")\n";

            return $pk;
        }

        return $pk;
    }

    public function addAnnotation($name, $value) {
        // TODO: Implement addAnnotation() method.
    }
}