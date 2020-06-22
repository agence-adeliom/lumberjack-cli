<?php


namespace Adeliom\WP\CLI\Commands;

use Adeliom\WP\CLI\Parser;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

class CronMake extends MakeFromStubCommand
{
    protected $signature = 'make:cron {name : The class name of the Cron Job}';

    protected $description = 'Create a Cron Job';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Create a new Cron Job');

        $name = $input->getArgument('name');

        $helper = new QuestionHelper;

        $question = new ConfirmationQuestion('<info>Register Cron Job from Config? (y/n)</info> [default: y] ');
        $register = $helper->ask($input, $output, $question);

        $stub = file_get_contents(__DIR__ . '/stubs/Cron.stub');
        $stub = str_replace('DummyCron', $name, $stub);


        try {
            $this->createFile('app/Crons/'.$name.'.php', $stub);
            $io->success('The Cron Job  "'.$name.'" was created. - File : ' . 'app/Crons/'.$name.'.php');

            if ($register) {
                $this->registerCronJobInConfig($name);
                $io->success('The Cron Job "'.$name.'" was registred in config file : ' . $this->app->basePath() . '/config/crons.php');
            }
            return 1;
        } catch (\Exception $e) {
            $io->error($e->getMessage());
            return 0;
        }
    }

    protected function registerCronJobInConfig($name)
    {
        $configPath = $this->app->basePath() . '/config/crons.php';
        $config = file_get_contents($configPath);
        $config = str_replace("'register' => [", "'register' => [\n\t\tApp\Crons\\".$name."::class,", $config);
        file_put_contents($configPath, $config);
    }
}
