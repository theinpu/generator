<?php
/**
 * User: inpu
 * Date: 25.10.13 14:50
 */

namespace bc\generator\struct;

use bc\generator\Parser;

class DataMapDescription extends ClassDescription {

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
        $this->parser = $parser;
        parent::__construct($name, $this->getNameSpaceFromPath('dataMap', $parser->getNamespace()));
        $this->useDoc();
        $this->modelClass = $parser->getNamespace() . '\\' . $parser->getClass();
        $this->setParent('DataMap');
        $this->createInitSql();
        $this->createInsertBindings();
        $this->createUpdateBindings();
        $this->createItemCallBack();
    }

    private function generateInsert() {
        $sql = "INSERT INTO `" . $this->parser->getTable() . "` (";
        $fields = array();
        foreach ($this->parser->getFields() as $name => $info) {
            if (is_null($info->getSqlType())) continue;
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
            if (is_null($info->getSqlType())) continue;
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
        $code = explode("\n", $code);
        $initSql->setCode($code);

        $this->addMethod($initSql);
    }

    private function createInsertBindings() {
        $insertBindings = new MethodDescription('getInsertBindings');
        $insertBindings->setModifier('protected');
        $insertBindings->setType('array');
        $param = new ParamDescription('item');
        $param->setType($this->modelClass);
        $param->setNameSpace($this->parser->getNamespace());
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
        $param->setNameSpace($this->parser->getNamespace());
        $updateBindings->addParam($param);

        $code = $this->generateBindingCode();
        $updateBindings->setCode($code);

        $this->addMethod($updateBindings);
    }

    /**
     * @return string
     */
    private function generateBindingCode() {
        $code[] = "return array(";

        foreach ($this->parser->getFields() as $field => $info) {
            if (is_null($info->getSqlType())) continue;
            $getter = $info->getter() . '()';
            if ($info->isRef()) {
                $getter .= '->' . $info->getRef();
            }
            $code[] = $this->indent("':{$field}' => \$item->{$getter},");
        }
        $code[] = ");";
        return $code;
    }

    private function generateFieldList() {
        $list = array('`id`');
        foreach ($this->parser->getFields() as $field => $info) {
            $list[] = '`' . $field . '`';
        }
        return implode(', ', $list);
    }

    private function createItemCallBack() {
        $callbacks = array(
            'before' => array(),
            'after' => array()
        );
        foreach ($this->parser->getFields() as $field) {
            if ($field->getType() == '\DateTime') {
                $callback = '$item->' . $field->setter()
                    . '(\DateTime::createFromFormat(\'U\', $item->' . $field->getter() . "()));";
                if ($field->isReadOnly()) {
                    $callbacks['before'][] = $callback;
                } else {
                    $callbacks['after'][] = $callback;
                }
            }
        }
        $method = new MethodDescription('itemCallback');
        $method->setModifier('protected');
        $param = new ParamDescription('item');
        $param->setType($this->modelClass);
        $param->setNameSpace($this->parser->getNamespace());
        $method->addParam($param);
        $code = array();
        $code = array_merge($code, $callbacks['before']);
        $code[] = 'parent::itemCallback($item);';
        $code = array_merge($code, $callbacks['after']);
        $method->setCode($code);
        $this->addMethod($method);
    }


    protected function getNameSpaceFromPath($component, $base = '') {
        $namespace = trim($this->parser->getPath($component), '/');
        $namespace = str_replace('/', '\\', $namespace);
        $namespace = trim($base, '\\') . '\\' . $namespace;
        $namespace = explode('\\', $namespace);

        $path = array();
        foreach ($namespace as $id => $item) {
            if ($item == '.') continue;
            if ($item == '..') {
                if ($id > 0) {
                    unset($path[$id - 1]);
                }
                continue;
            }
            $path[] = $item;
        }

        $namespace = implode('\\', $path);

        return $namespace;
    }

} 