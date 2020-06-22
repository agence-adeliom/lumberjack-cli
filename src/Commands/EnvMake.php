<?php


namespace Adeliom\WP\CLI\Commands;

use Adeliom\WP\CLI\Parser;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class EnvMake extends MakeFromStubCommand
{
    protected $signature = 'make:env {name : env name}';

    protected $description = 'Create a environement';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Create a new environement');

        $name = $input->getArgument('name');
        $name = Parser::slugify($name);

        $stub = file_get_contents(__DIR__ . '/stubs/environment.stub');
        $stub = str_replace('dummy-env', $name, $stub);

        $this->createFile('../../../../config/environments/'.$name.'.php', $stub);
        $io->success('The environement  "'.$name.'" was created.');

        return 1;
    }
}
