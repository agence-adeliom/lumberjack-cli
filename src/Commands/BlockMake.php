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
    protected $signature = 'make:block {name : The class name of the FlexibleLayout}';

    protected $description = 'Create a Gutenberg block';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Create a new block');

        $name      = $input->getArgument('name');
        $converter = new Convert($name);
        $name = $converter->toPascal();
        $name      = str_replace("Block", "", $name);
        $groupName = $name;
        $name      .= "Block";
        $key       = $converter->toKebab();
        $twigKey       = $key;

        $stub = file_get_contents(__DIR__ . '/stubs/Block/class.stub');
        $stub = str_replace('DummyBlock', $name, $stub);
        $stub = str_replace('Dummy', $groupName, $stub);
        $stub = str_replace('dummy-key', $twigKey, $stub);

        $stubView = file_get_contents(__DIR__ . '/stubs/Block/view.twig.stub');
        $stubView = str_replace('DummyLayout', $name, $stubView);
        $stubView = str_replace('Dummy', $groupName, $stubView);
        $stubView = str_replace('dummy-key', $twigKey, $stubView);

        try {
            $this->createFile('app/Blocks/' . $name . '.php', $stub);
            $io->success('The block  "' . $name . '" was created in file ' . 'app/Blocks/' . $name . '.php');

            $this->createFile('views/blocks/' . $key . '.html.twig', $stubView);
            $io->success('The block view "' . $key . '" was created in file ' . 'views/blocks/' . $key . '.html.twig');

            $this->createFile('assets/blocks/'.$key.'/styles.scss', "");
            $io->success('The block styles "' . $key . '" was created in file ' . 'assets/blocks/'.$key.'/styles.scss');

            $this->createFile('assets/blocks/'.$key.'/scripts.js', "");
            $io->success('The block scripts "' . $key . '" was created in file ' . 'assets/blocks/'.$key.'/scripts.js');

            return 1;
        } catch (Exception $e) {
            $io->error($e->getMessage());
            return 0;
        }
    }
}
