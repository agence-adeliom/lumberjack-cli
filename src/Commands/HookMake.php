<?php


namespace Adeliom\WP\CLI\Commands;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

class HookMake extends MakeFromStubCommand
{
    protected $signature = 'make:hook {name : The class name of the Hooks Class}';

    protected $description = 'Create a Hooks Class';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Create a new Hook Class');

        $name = $input->getArgument('name');
        $name = str_replace("Hooks", "", $name);
        $name .= "Hooks";

        $helper = new QuestionHelper;

        $question = new ConfirmationQuestion('<info>Register Hook from Config? (y/n)</info> [default: y] ');
        $register = $helper->ask($input, $output, $question);

        $stub = file_get_contents(__DIR__ . '/stubs/Hooks.stub');
        $stub = str_replace('DummyHooks', $name, $stub);

        try {
            $this->createFile('app/Hooks/'.$name.'.php', $stub);
            $io->success('The Hook Class  "'.$name.'" was created. - File : ' . 'app/Hooks/'.$name.'.php');
            if ($register) {
                $this->registerHookInConfig($name);
                $io->success('The Hook Class "'.$name.'" was registred in config file : ' . $this->app->basePath() . '/config/hooks.php');
            }
            return 1;
        } catch (\Exception $e) {
            $io->error($e->getMessage());
            return 0;
        }
    }

    protected function registerHookInConfig($name)
    {
        $configPath = $this->app->basePath() . '/config/hooks.php';
        $config = file_get_contents($configPath);
        $config = str_replace("'register' => [", "'register' => [\n\t\tApp\Hooks\\".$name."::class,", $config);
        file_put_contents($configPath, $config);
    }
}
