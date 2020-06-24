<?php


namespace Adeliom\WP\CLI\Commands;

use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AdminMake extends MakeFromStubCommand
{
    protected $signature = 'make:admin {name : The class name of the Admin}';

    protected $description = 'Create a Admin';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Create a new Admin class');

        $name      = $input->getArgument('name');
        $name      = str_replace("Admin", "", $name);
        $groupName = $name;
        $name      .= "Admin";

        $stub = file_get_contents(__DIR__ . '/stubs/Admin.stub');
        $stub = str_replace('DummyAdmin', $name, $stub);
        $stub = str_replace('Dummy', $groupName, $stub);

        try {
            $this->createFile('app/Admin/' . $name . '.php', $stub);
            $io->success('The Admin class "' . $name . '" was created in file ' . 'app/Admin/' . $name . '.php');
            return 1;
        } catch (Exception $e) {
            $io->error($e->getMessage());
            return 0;
        }
    }
}
