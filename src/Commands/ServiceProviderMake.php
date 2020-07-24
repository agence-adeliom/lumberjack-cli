<?php


namespace Adeliom\WP\CLI\Commands;

use Exception;
use Jawira\CaseConverter\Convert;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

class ServiceProviderMake extends MakeFromStubCommand
{
    protected $signature = 'make:provider {name : The class name of the ServiceProvider}';

    protected $description = 'Create a ServiceProvider';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Create a new ServiceProvider');

        $name = $input->getArgument('name');
        $converter = new Convert($name);
        $name = $converter->toPascal();
        $name = str_replace("ServiceProvider", "", $name);
        $name .= "ServiceProvider";

        $helper = new QuestionHelper;

        $question = new ConfirmationQuestion('<info>Register ServiceProvider from Config? (y/n)</info> [default: y] ');
        $register = $helper->ask($input, $output, $question);

        $stub = file_get_contents(__DIR__ . '/stubs/ServiceProvider.stub');
        $stub = str_replace('DummyServiceProvider', $name, $stub);

        try {
            $this->createFile('app/Providers/' . $name . '.php', $stub);
            $io->success('The Providers "' . $name . '" was created in file ' . 'app/Providers/' . $name . '.php');
            if ($register) {
                $this->registerServiceProviderInConfig($name);
                $io->success('The Providers "' . $name . '" was registred in config file : ' . $this->app->basePath() . '/config/app.php');
            }
            return 1;
        } catch (Exception $e) {
            $io->error($e->getMessage());
            return 0;
        }
    }

    protected function registerServiceProviderInConfig($name)
    {
        $configPath = $this->app->basePath() . '/config/app.php';
        $config     = file_get_contents($configPath);
        $config     = str_replace("'providers' => [", "'providers' => [\n\t\tApp\Providers\\" . $name . "::class,", $config);
        file_put_contents($configPath, $config);
    }
}
