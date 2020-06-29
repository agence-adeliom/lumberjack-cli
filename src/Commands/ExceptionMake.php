<?php


namespace Adeliom\WP\CLI\Commands;

use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ExceptionMake extends MakeFromStubCommand
{
    protected $signature = 'make:exception {name : The class name of the Exception}';

    protected $description = 'Create a Exception';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Create a new Exception');

        $name = $input->getArgument('name');
        $name = str_replace("Exception", "", $name);
        $name .= "Exception";


        $stub = file_get_contents(__DIR__ . '/stubs/Exception.stub');
        $stub = str_replace('DummyException', $name, $stub);

        try {
            $this->createFile('app/Exceptions/' . $name . '.php', $stub);
            $io->success('The Event Listener  "' . $name . '" was created in file ' . 'app/Exceptions/' . $name . '.php');
            return 1;
        } catch (Exception $e) {
            $io->error($e->getMessage());
            return 0;
        }
    }
}
