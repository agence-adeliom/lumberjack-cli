<?php


namespace Adeliom\WP\CLI\Commands;

use Adeliom\WP\CLI\Parser;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class LayoutMake extends MakeFromStubCommand
{
    protected $signature = 'make:flex-layout {name : The class name of the FlexibleLayout}';

    protected $description = 'Create a FlexibleLayout';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Create a new Layout');

        $name = $input->getArgument('name');
        $name = str_replace("Layout", "", $name);
        $groupName = $name;
        $name .= "Layout";
        $key = Parser::slugify($name);

        $stub = file_get_contents(__DIR__ . '/stubs/Layout.stub');
        $stub = str_replace('DummyLayout', $name, $stub);
        $stub = str_replace('Dummy', $groupName, $stub);
        $stub = str_replace('dummy-key', $key, $stub);

        $stubView = file_get_contents(__DIR__ . '/stubs/Layout.html.stub');
        $stubView = str_replace('DummyLayout', $name, $stubView);
        $stubView = str_replace('Dummy', $groupName, $stubView);
        $stubView = str_replace('dummy-key', $key, $stubView);

        try {
            $this->createFile('app/FlexibleLayout/'.$name.'.php', $stub);
            $io->success('The Layout  "'.$name.'" was created. - File : ' . 'app/FlexibleLayout/'.$name.'.php');

            $this->createFile('view/layouts/'.$key.'.html.twig', $stubView);
            $io->success('The Layout view "'.$key.'" was created. - File : ' . 'view/layouts/'.$key.'.html.twig');

            return 1;
        } catch (\Exception $e) {
            $io->error($e->getMessage());
            return 0;
        }
    }
}
