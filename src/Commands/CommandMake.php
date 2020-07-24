<?php


namespace Adeliom\WP\CLI\Commands;

use Exception;
use Jawira\CaseConverter\Convert;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

class CommandMake extends MakeFromStubCommand
{
    protected $signature = 'make:command {name : The class name of the command}';

    protected $description = 'Create a command';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Create a new command');

        $name = $input->getArgument('name');
        $converter = new Convert($name);
        $name = $converter->toPascal();

        $helper = new QuestionHelper;

        $question = new ConfirmationQuestion('<info>Register the command from Config? (y/n)</info> [default: y] ');
        $register = $helper->ask($input, $output, $question);

        $stub = file_get_contents(__DIR__ . '/stubs/Command.stub');
        $stub = str_replace('DummyCommand', $name, $stub);

        $slug = $converter->toKebab();
        $stub = str_replace('dummy-command', $slug, $stub);

        try {
            $this->createFile('app/Commands/' . $name . '.php', $stub);
            $io->success('The command  "' . $name . '" was created in file ' . 'app/Commands/' . $name . '.php');

            if ($register) {
                $this->registerCommandInConfig($name);
                $io->success('The command "' . $name . '" was registred in config file : ' . $this->app->basePath() . '/config/commands.php');
            }
            return 1;
        } catch (Exception $e) {
            $io->error($e->getMessage());
            return 0;
        }
    }

    protected function registerCommandInConfig($name)
    {
        $configPath = $this->app->basePath() . '/config/commands.php';
        $config     = file_get_contents($configPath);
        $config     = str_replace("return [", "return [\n\t\tApp\Commands\\" . $name . "::class,", $config);
        file_put_contents($configPath, $config);
    }
}
