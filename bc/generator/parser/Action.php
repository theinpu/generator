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
    private $template = array();
    private $redirect = null;
    private $vars = null;
    private $todo = '';
    private $url = '';
    private $methods = array('get');

    function __construct($name, $actionInfo) {
        $this->name = $name;

        if(isset($actionInfo['params'])) {
            foreach($actionInfo['params'] as $param => $info) {
                $default = isset($info['default']) ? $info['default'] : null;
                $this->params[$param] = new ParamDescription($param, $default);
            }
        }
        if(isset($actionInfo['todo'])) {
            $this->todo = $actionInfo['todo'];
        }
        if(isset($actionInfo['template'])) {
            if(is_array($actionInfo['template'])) {
                $this->template = $actionInfo['template'];
            } else {
                $this->template['name'] = $actionInfo['template'];
            }
        }
        if(isset($actionInfo['redirect'])) {
            $this->redirect = $actionInfo['redirect'];
        }
        if(isset($actionInfo['vars'])) {
            $this->vars = $actionInfo['vars'];
        }
        if(isset($actionInfo['url'])) {
            $this->url = $actionInfo['url'];
        }
        if(isset($actionInfo['methods'])) {
            $this->methods = $actionInfo['methods'];
        }
    }

    public function getName() {
        return $this->name;
    }

    public function getParams() {
        return $this->params;
    }

    public function getTemplateCode() {
        $code = array();
        if(isset($this->template['vars'])) {
            foreach($this->template['vars'] as $name => $value) {
                if(strpos($value, ':', 0) === 0) {
                    $value = str_replace(':', '$', $value);
                }
                $code[] = '$this->addData(array(\''.$name.'\' => '.$value.'));';
            }
        }
        $code[] = '$this->template(\''.$this->template['name'].'\');';

        return $code;
    }

    public function hasTemplate() {
        return isset($this->template['name']);
    }

    public function hasRedirect() {
        return !is_null($this->redirect);
    }

    public function getRedirectCode() {
        $url = $this->redirect['url'];
        foreach($this->redirect['params'] as $param) {
            $url = str_replace(':'.$param, '\'.$'.$param.'.\'', $url);
        }
        $code = isset($this->redirect['code']) ? ', '.$this->redirect['code'] : '';

        return '$this->getSlim()->redirect(\''.$url.'\''.$code.');';
    }

    public function hasVars() {
        return !is_null($this->vars);
    }

    public function getVarsCode() {
        $code = array();
        foreach($this->vars as $name => $value) {
            if(strpos($value, ':', 0) === 0) {
                $value = str_replace(':', '$', $value);
            }
            if('$'.$name == $value) continue;
            $code[] = '$'.$name.' = '.$value;
        }

        return $code;
    }

    public function hasToDo() {
        return !empty($this->todo);
    }

    public function getToDoCode() {
        return array('//TODO: '.$this->todo);
    }

    public function getUrl() {
        return $this->url;
    }

    public function getMethods() {
        return $this->methods;
    }
}