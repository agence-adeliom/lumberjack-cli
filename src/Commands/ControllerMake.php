<?php


namespace Adeliom\WP\CLI\Commands;

use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ControllerMake extends MakeFromStubCommand
{
    protected $signature = 'make:controller {name : The class name of the Controller}';

    protected $description = 'Create a Controller';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Create a new Controller');

        $name = $input->getArgument('name');
        $name = str_replace("Controller", "", $name);
        $name .= "Controller";

        $stub = file_get_contents(__DIR__ . '/stubs/Controller.stub');
        $stub = str_replace('DummyController', $name, $stub);

        $this->createFile('app/Http/Controllers/' . $name . '.php', $stub);

        try {
            $this->createFile('app/Http/Controllers/' . $name . '.php', $stub);
            $io->success('The Controller  "' . $name . '" was created in file ' . 'app/Http/Controllers/' . $name . '.php');

            return 1;
        } catch (Exception $e) {
            $io->error($e->getMessage());
            return 0;
        }
    }
}
