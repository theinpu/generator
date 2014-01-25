<?php
/**
 * User: inpu
 * Date: 25.01.14
 * Time: 16:44
 */

namespace bc\generator;


use bc\config\ConfigManager;
use bc\generator\struct\ClassDescription;
use bc\generator\struct\MethodDescription;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ControllerGeneratorCommand extends Command {

    const DefaultNamespace = 'bc\cmf\Controller';

    protected function configure() {
        $this->setName('ctrl');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $parser = new ControllerParser(ConfigManager::get('config/generator')->get('def.path') . 'ctrl/ec.yaml');
        $class = new ClassDescription($parser->getClass(), $parser->getNamespace());
        $class->setParent(is_null($parser->getParent()) ? self::DefaultNamespace : $parser->getParent());
        foreach ($parser->getActions() as $action) {
            $method = new MethodDescription($action->getName());
            foreach ($action->getParams() as $param) {
                $method->addParam($param);
                if ($action->hasVars()) {
                    $method->appendCode($action->getVarsCode());
                }
                if ($action->hasTemplate()) {
                    $method->appendCode($action->getTemplateCode());
                }
                if ($action->hasRedirect()) {
                    $method->appendCode($action->getRedirectCode());
                }
            }
            $class->addMethod($method);
        }
        $output->writeln($class->export(false));
    }


} 