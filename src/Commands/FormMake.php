<?php


namespace Adeliom\WP\CLI\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FormMake extends MakeFromStubCommand
{
    protected $signature = 'make:form {name : The class name of the Form}';

    protected $description = 'Create a Form';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Create a new Form');

        $name = $input->getArgument('name');
        $name = str_replace("Form", "", $name);
        $name .= "Form";

        $stub = file_get_contents(__DIR__ . '/stubs/Form.stub');
        $stub = str_replace('DummyForm', $name, $stub);

        try {
            $this->createFile('app/Forms/'.$name.'.php', $stub);
            $io->success('The Form  "'.$name.'" was created. - File : ' . 'app/Forms/'.$name.'.php');
            return 1;
        } catch (\Exception $e) {
            $io->error($e->getMessage());
            return 0;
        }
    }
}
