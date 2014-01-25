<?php
/**
 * User: inpu
 * Date: 25.01.14
 * Time: 19:48
 */

namespace bc\generator\parser;


use bc\generator\struct\ParamDescription;

class Action {

    private $name;
    /**
     * @var ParamDescription[]
     */
    private $params = array();
    private $template = null;
    private $redirect = null;
    private $vars = null;

    function __construct($name, $actionInfo) {
        $this->name = $name;

        if (isset($actionInfo['params'])) {
            foreach ($actionInfo['params'] as $param => $info) {
                $default = isset($info['default']) ? $info['default'] : null;
                $this->params[$param] = new ParamDescription($param, $default);
            }
        }
        if (isset($actionInfo['template'])) {
            $this->template = $actionInfo['template'];
        }
        if (isset($actionInfo['redirect'])) {
            $this->redirect = $actionInfo['redirect'];
        }
        if (isset($actionInfo['vars'])) {
            $this->vars = $actionInfo['vars'];
        }
    }

    public function getName() {
        return $this->name;
    }

    public function getParams() {
        return $this->params;
    }

    public function getTemplateCode() {
        return '$this->template(\'' . $this->template . '\');';
    }

    public function hasTemplate() {
        return !is_null($this->template);
    }

    public function hasRedirect() {
        return !is_null($this->redirect);
    }

    public function getRedirectCode() {
        $url = $this->redirect['url'];
        foreach ($this->redirect['params'] as $param) {
            $url = str_replace(':' . $param, '\'.$' . $param . '.\'', $url);
        }
        $code = isset($this->redirect['code']) ? ', ' . $this->redirect['code'] : '';
        return '$this->getSlim()->redirect(\'' . $url . '\'' . $code . ');';
    }

    public function hasVars() {
        return !is_null($this->vars);
    }

    public function getVarsCode() {
        $code = array();
        foreach ($this->vars as $name => $value) {
            if (strpos($value, ':', 0) === 0) {
                $value = str_replace(':', '$', $value);
            }
            $code[] = '$this->addData(array(\'' . $name . '\' => ' . $value . '));';
        }
        return $code;
    }
}