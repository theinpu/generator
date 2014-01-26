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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ControllerGeneratorCommand extends Command {

    const DefaultController = 'Controller';
    const DefaultNamespace = 'bc\cmf';
    /**
     * @var ControllerParser
     */
    private $parser = null;
    /**
     * @var ClassDescription
     */
    private $class = null;
    /**
     * @var ClassDescription
     */
    private $command = null;

    protected function configure() {
        $this->setName('ctrl')
            ->setDescription('сгенерить контроллер')
            ->addArgument('description', InputArgument::REQUIRED, 'файл с описание контролла')
            ->addOption('command', 'c', InputOption::VALUE_NONE, 'генерить класс комманды')
            ->addOption('output', '-o', InputOption::VALUE_NONE, 'сохранять в файлики');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $cfg = ConfigManager::get('config/generator');
        $this->parser = new ControllerParser(
            $cfg->get('def.path') . $input->getArgument('description') . '.yaml');
        $this->class = $this->generateController();
        $save = $input->getOption('output');
        ob_start();
        $path = $this->parser->generatePath($this->parser->getNamespace() . '\\' . $this->parser->getClass());
        $file = $cfg->get('save.path') . $path['path'] . '/' . $path['file'];
        $this->output($save, $file, $this->class);
        $output->writeln(ob_get_clean());
        if ($input->getOption('command')) {
            $this->command = $this->generateCommand();
            ob_start();
            $path = $this->parser->generatePath($this->parser->getCommandNamespace() . '\\' . $this->parser->getCommandClass());
            $file = $cfg->get('save.path') . $path['path'] . '/' . $path['file'];
            $this->output($save, $file, $this->command);
            $output->writeln(ob_get_clean());
        }
    }

    /**
     * @return ClassDescription
     */
    private function generateController() {
        $class = new ClassDescription($this->parser->getClass(),
            $this->parser->getNamespace($this->parser->getFullClass()));
        if (is_null($this->parser->getParent())) {
            $class->setParent(self::DefaultController);
            $class->addUsage(self::DefaultNamespace . '\\' . self::DefaultController);
        }
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

        if ($command->getNamespace() != self::DefaultNamespace) {
            $command->addUsage(self::DefaultNamespace . '\Command');
        }

        $construct = new MethodDescription('__construct');
        $construct->addParam(new ParamDescription('method'));
        $construct->appendCode('parent::__construct(\'' . $this->parser->getFullClass() . '\', $method);');
        $command->addMethod($construct);

        foreach ($this->parser->getActions() as $action) {
            $cmd = new MethodDescription($action->getName());
            $cmd->setStatic(true);
            $cmd->setType($this->parser->getCommandClass());
            $cmd->appendCode('return new self(\'' . $action->getName() . '\');');
            $command->addMethod($cmd);
        }
        return $command;
    }

    /**
     * @param $save
     * @param $file
     * @param ClassDescription $class
     */
    protected function output($save, $file, $class) {
        if ($save) {
            $writer = new ClassWriter($class, $file);
            $writer->write();
            echo $class->getName() . ' saved to ' . realpath($file) . "\n";
        } else {
            $this->class->export();
        }
    }


} 