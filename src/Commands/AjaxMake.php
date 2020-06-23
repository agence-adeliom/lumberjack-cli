<?php


namespace Adeliom\WP\CLI\Commands;

use Adeliom\WP\CLI\Parser;
use Exception;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

class AjaxMake extends MakeFromStubCommand
{
    protected $signature = 'make:ajax {name : The class name of the Ajax Action}';

    protected $description = 'Create a Ajax Action';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Create a new Ajax Action');

        $name = $input->getArgument('name');
        $name = str_replace("Action", "", $name);
        $name .= "Action";

        $helper = new QuestionHelper;

        $question = new ConfirmationQuestion('<info>Register Ajax Action from Config? (y/n)</info> [default: y] ');
        $register = $helper->ask($input, $output, $question);

        $slug = Parser::slugify($name);

        $stub = file_get_contents(__DIR__ . '/stubs/Action.stub');
        $stub = str_replace('DummyAction', $name, $stub);
        $stub = str_replace('dummy-action-name', $slug, $stub);


        try {
            $this->createFile('app/Actions/' . $name . '.php', $stub);
            $io->success('The Ajax action  "' . $name . '" was created. - File : ' . 'app/Actions/' . $name . '.php');

            if ($register) {
                $this->registerAjaxActionInConfig($name);
                $io->success('The Ajax action  "' . $name . '" was registred in config file : ' . $this->app->basePath() . '/config/actions.php');
            }

            return 1;
        } catch (Exception $e) {
            $io->error($e->getMessage());
            return 0;
        }
    }

    protected function registerAjaxActionInConfig($name)
    {
        $configPath = $this->app->basePath() . '/config/actions.php';
        $config     = file_get_contents($configPath);
        $config     = str_replace("'register' => [", "'register' => [\n\t\tApp\Actions\\" . $name . "::class,", $config);
        file_put_contents($configPath, $config);
    }
}
