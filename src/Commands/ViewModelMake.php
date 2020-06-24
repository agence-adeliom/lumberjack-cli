<?php


namespace Adeliom\WP\CLI\Commands;

use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ViewModelMake extends MakeFromStubCommand
{
    protected $signature = 'make:viewmodel {name : The class name of the View Model}';

    protected $description = 'Create a ViewModel';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Create a new ViewModel');

        $name = $input->getArgument('name');
        $name = str_replace("ViewModel", "", $name);
        $name .= "ViewModel";

        $stub = file_get_contents(__DIR__ . '/stubs/ViewModel.stub');
        $stub = str_replace('DummyViewModel', $name, $stub);

        try {
            $this->createFile('app/ViewModels/' . $name . '.php', $stub);
            $io->success('The ViewModels "' . $name . '" was created in file ' . 'app/ViewModels/' . $name . '.php');

            return 1;
        } catch (Exception $e) {
            $io->error($e->getMessage());
            return 0;
        }
    }
}
