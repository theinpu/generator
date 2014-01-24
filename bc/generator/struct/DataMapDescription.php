<?php
/**
 * User: inpu
 * Date: 25.10.13 14:50
 */

namespace bc\generator\struct;

use bc\generator\Parser;

class DataMapDescription extends ClassDescription
{

    /**
     * @var Parser
     */
    private $parser;
    private $modelClass;

    /**
     * @param $name
     * @param Parser $parser
     */
    public function __construct($name, $parser) {
        parent::__construct($name, $parser->getNamespace() . '\\' . $parser->getPath('dataMap', true));
        $this->useDoc();
        $this->parser = $parser;
        $this->modelClass = $parser->getNamespace() . '\\' . $parser->getClass();
        $this->setParent('DataMap');
        $this->createInitSql();
        $this->createInsertBindings();
        $this->createUpdateBindings();
    }

    private function generateInsert() {
        $sql = "INSERT INTO `" . $this->parser->getTable() . "` (";
        $fields = array();
        foreach ($this->parser->getFields() as $name => $info) {
            if (!isset($info['sqlType'])) continue;
            $fields[] = $name;
        }
        $names = array();
        $binds = array();
        foreach ($fields as $field) {
            if ($field == "id") continue;
            $names[] = $field;
            $binds[] = ':' . $field;
        }
        $sql .= implode(', ', $names);
        $sql .= ") VALUES (" . implode(', ', $binds);
        $sql .= ")";

        return $sql;
    }

    private function generateUpdate() {
        $sql = "UPDATE " . $this->parser->getTable() . " SET ";
        $fields = array();
        foreach ($this->parser->getFields() as $name => $info) {
            if (!isset($info['sqlType'])) continue;
            $fields[] = $name;
        }
        $sets = array();
        foreach ($fields as $field) {
            if ($field == "id") continue;
            if ($this->parser->checkFlag($field, 'RO')) continue;
            $sets[] = $field . "=:" . $field;
        }
        $sql .= implode(', ', $sets);
        $sql .= " WHERE id=:id";

        return $sql;
    }

    private function createInitSql() {
        $initSql = new MethodDescription('initSql');
        $initSql->setModifier('protected');

        $fieldList = $this->generateFieldList();

        $findOneSql = "SELECT " . $fieldList . " FROM " . $this->parser->getTable() . " WHERE id=:id";
        $findAllSql = "SELECT " . $fieldList . " FROM " . $this->parser->getTable();
        $findByIdsSql = "SELECT " . $fieldList . " FROM " . $this->parser->getTable() . " WHERE id IN (:ids)";
        $countSql = "SELECT count(id) FROM " . $this->parser->getTable();
        $insertSql = $this->generateInsert();
        $updateSql = $this->generateUpdate();
        $deleteSql = "DELETE FROM " . $this->parser->getTable() . " WHERE id=:id";

        $class = addslashes(ltrim($this->modelClass, '\\'));
        $code = <<<CODE
\$this->className = '$class';
\$this->findOneSql = "$findOneSql";
\$this->findAllSql = "$findAllSql";
\$this->findByIdsSql = "$findByIdsSql";
\$this->countSql = "$countSql";
\$this->insertSql = "$insertSql";
\$this->updateSql = "$updateSql";
\$this->deleteSql = "$deleteSql";
CODE;
        $initSql->setCode($code);

        $this->addMethod($initSql);
    }

    private function createInsertBindings() {
        $insertBindings = new MethodDescription('getInsertBindings');
        $insertBindings->setModifier('protected');
        $insertBindings->setType('array');
        $param = new ParamDescription('item');
        $param->setType($this->modelClass);
        $insertBindings->addParam($param);
        $code = $this->generateBindingCode();

        $insertBindings->setCode($code);

        $this->addMethod($insertBindings);
    }

    private function createUpdateBindings() {
        $updateBindings = new MethodDescription('getUpdateBindings');
        $updateBindings->setModifier('protected');
        $updateBindings->setType('array');
        $param = new ParamDescription('item');
        $param->setType($this->modelClass);
        $updateBindings->addParam($param);
        $code = $this->generateBindingCode();
        $updateBindings->setCode($code);

        $this->addMethod($updateBindings);
    }

    /**
     * @return string
     */
    private function generateBindingCode() {
        $code = "return array(\n";

        foreach ($this->parser->getFields() as $field => $info) {
            if (!isset($info['sqlType'])) continue;
            $getter = 'get' . ucfirst($field) . '()';
            if (isset($info['get'])) {
                if (isset($info['get']['name'])) {
                    $getter = $info['get']['name'] . '()';
                }
            }
            if (isset($info['ref'])) {
                $getter .= '->' . $info['ref'] . '()';
            }
            $code .= "':{$field}' => \$item->{$getter},\n";
        }
        $code .= ");";
        return $code;
    }

    private function generateFieldList() {
        $list = array('`id`');
        foreach ($this->parser->getFields() as $field => $info) {
            $list[] = '`' . $field . '`';
        }
        return implode(', ', $list);
    }

} 