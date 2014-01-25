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
use bc\generator\struct\ParamDescription;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ControllerGeneratorCommand extends Command {

    const DefaultNamespace = 'bc\cmf\Controller';
    /**
     * @var ControllerParser
     */
    private $parser = null;

    protected function configure() {
        $this->setName('ctrl')
            ->setDescription('сгенерить контроллер')
            ->addOption('command', 'c', InputOption::VALUE_NONE, 'генерить класс комманды');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->parser = new ControllerParser(
            ConfigManager::get('config/generator')->get('def.path') . 'ctrl/ec.yaml');
        $class = $this->generateController();
        $output->writeln($class->export());
        if ($input->getOption('command')) {
            $command = $this->generateCommand();
            $output->writeln($command->export());
        }
    }

    /**
     * @return ClassDescription
     */
    private function generateController() {
        $class = new ClassDescription($this->parser->getClass(),
            $this->parser->getNamespace($this->parser->getFullClass()));
        $class->setParent(is_null($this->parser->getParent())
            ? self::DefaultNamespace : $this->parser->getParent());
        foreach ($this->parser->getActions() as $action) {
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
        return $class;
    }

    /**
     * @return ClassDescription
     */
    private function generateCommand() {
        $command = new ClassDescription($this->parser->getCommandClass(),
            $this->parser->getCommandNamespace());
        $command->setParent('Command');

        $construct = new MethodDescription('__construct');
        $construct->addParam(new ParamDescription('method'));
        $construct->appendCode('parent::__construct(\'' . $this->parser->getFullClass() . '\', $method);');
        $command->addMethod($construct);

        foreach ($this->parser->getActions() as $action) {
            $cmd = new MethodDescription($action->getName());
            $cmd->setType($this->parser->getCommandClass());
            $cmd->appendCode('return new self(\'' . $action->getName() . '\');');
            $command->addMethod($cmd);
        }
        return $command;
    }


} 