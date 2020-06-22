<?php


namespace Adeliom\WP\CLI\Commands;

use Adeliom\WP\CLI\Parser;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

class EventMake extends MakeFromStubCommand
{
    protected $signature = 'make:event {name : The class name of the Event Listener}';

    protected $description = 'Create a Event Listener';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Create a new Event Listener');

        $name = $input->getArgument('name');
        $name = str_replace("Event", "", $name);

        $slug = Parser::slugify($name);

        $name .= "Event";

        $helper = new QuestionHelper;
        $question = new ConfirmationQuestion('<info>Register Event Listener from Config? (y/n)</info> [default: y] ');
        $register = $helper->ask($input, $output, $question);

        $stub = file_get_contents(__DIR__ . '/stubs/Event.stub');
        $stub = str_replace('DummyEvent', $name, $stub);
        $stub = str_replace('dummy-event', $slug, $stub);

        try {
            $this->createFile('app/Events/'.$name.'.php', $stub);
            $io->success('The Event Listener  "'.$name.'" was created. - File : ' . 'app/Events/'.$name.'.php');

            if ($register) {
                $this->registerEventListenerInConfig($name);
                $io->success('The Event Listener "'.$name.'" was registred in config file : ' . $this->app->basePath() . '/config/events.php');
            }
            return 1;
        } catch (\Exception $e) {
            $io->error($e->getMessage());
            return 0;
        }
    }

    protected function registerEventListenerInConfig($name)
    {
        $configPath = $this->app->basePath() . '/config/events.php';
        $config = file_get_contents($configPath);
        $config = str_replace("'listener' => [", "'listener' => [\n\t\tApp\Events\\".$name."::class,", $config);
        file_put_contents($configPath, $config);
    }
}
