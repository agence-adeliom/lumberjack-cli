<?php


namespace Adeliom\WP\CLI\Commands;

use Exception;
use Illuminate\Support\Str;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

class TwigExtensionMake extends MakeFromStubCommand
{
    protected $signature = 'make:twig-extension {name : The class name of the Twig Extension}';

    protected $description = 'Create a Twig Extension';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Create a new Twig Extension');

        $name = $input->getArgument('name');
        $name = Str::camel($name);
        $name = str_replace("TwigExtension", "", $name);
        $name .= "TwigExtension";

        $helper = new QuestionHelper;

        $question = new ConfirmationQuestion('<info>Register TwigExtension from Config? (y/n)</info> [default: y] ');
        $register = $helper->ask($input, $output, $question);

        $stub = file_get_contents(__DIR__ . '/stubs/TwigExtension.stub');
        $stub = str_replace('DummyTwigExtension', $name, $stub);

        try {
            $this->createFile('app/TwigExtensions/' . $name . '.php', $stub);
            $io->success('The Twig Extension Class  "' . $name . '" was created in file ' . 'app/TwigExtensions/' . $name . '.php');
            if ($register) {
                $this->registerHookInConfig($name);
                $io->success('The Twig Extension Class "' . $name . '" was registred in config file : ' . $this->app->basePath() . '/config/twig.php');
            }
            return 1;
        } catch (Exception $e) {
            $io->error($e->getMessage());
            return 0;
        }
    }

    protected function registerHookInConfig($name)
    {
        $configPath = $this->app->basePath() . '/config/twig.php';
        $config     = file_get_contents($configPath);
        $config     = str_replace("'extensions' => [", "'extensions' => [\n\t\tApp\TwigExtensions\\" . $name . "::class,", $config);
        file_put_contents($configPath, $config);
    }
}
