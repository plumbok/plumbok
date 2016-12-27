<?php declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 27.12.16
 * Time: 09:21
 */
namespace Plumbok;

use Plumbok\Command\CompileCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;

foreach ([__DIR__ . '/../vendor/autoload.php', __DIR__ . '/../../../autoload.php'] as $file) {
    if (file_exists($file)) {
        require $file;
        break;
    }
}

/** @var Application $app */
$app = new class('Plumbok', '{version}') extends Application {
    /**
     * Gets the name of the command based on input.
     * @param InputInterface $input The input interface
     * @return string The command name
     */
    protected function getCommandName(InputInterface $input)
    {
        return 'compile';
    }

    /**
     * Gets the default commands that should always be available.
     * @return array An array of default Command instances
     */
    protected function getDefaultCommands()
    {
        // Keep the core default commands to have the HelpCommand
        // which is used when using the --help option
        $defaultCommands = parent::getDefaultCommands();
        $defaultCommands[] = new CompileCommand();

        return $defaultCommands;
    }

    /**
     * Overridden so that the application doesn't expect the command
     * name to be the first argument.
     */
    public function getDefinition()
    {
        $inputDefinition = parent::getDefinition();
        $inputDefinition->setArguments();

        return $inputDefinition;
    }
};
$app->run();
