<?php


namespace Adeliom\WP\CLI\Commands;

use Adeliom\WP\CLI\Parser;
use Exception;
use Jawira\CaseConverter\Convert;
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

        $name      = $input->getArgument('name');
        $converter = new Convert($name);
        $name = $converter->toPascal();
        $name      = str_replace("Layout", "", $name);
        $groupName = $name;
        $name      .= "Layout";
        $key       = $converter->toKebab();
        $twigKey       = $converter->toSnake();

        $stub = file_get_contents(__DIR__ . '/stubs/Layout.stub');
        $stub = str_replace('DummyLayout', $name, $stub);
        $stub = str_replace('Dummy', $groupName, $stub);
        $stub = str_replace('dummy-key', $twigKey, $stub);

        $stubView = file_get_contents(__DIR__ . '/stubs/Layout.twig.stub');
        $stubView = str_replace('DummyLayout', $name, $stubView);
        $stubView = str_replace('Dummy', $groupName, $stubView);
        $stubView = str_replace('dummy_key', $twigKey, $stubView);

        try {
            $this->createFile('app/FlexibleLayout/' . $name . '.php', $stub);
            $io->success('The Layout  "' . $name . '" was created in file ' . 'app/FlexibleLayout/' . $name . '.php');

            $this->createFile('views/layouts/' . $twigKey . '.html.twig', $stubView);
            $io->success('The Layout view "' . $twigKey . '" was created in file ' . 'views/layouts/' . $twigKey . '.html.twig');

            $this->createFile('assets/sass/components/flexible-contents/includes/_' . $key . '.scss', '// assets/sass/components/flexible-contents/includes/_' . $key . '.scss');
            $io->success('The Layout style "' . $key . '" was created in file ' . 'assets/sass/components/flexible-contents/includes/_' . $key . '.scss');

            return 1;
        } catch (Exception $e) {
            $io->error($e->getMessage());
            return 0;
        }
    }
}
