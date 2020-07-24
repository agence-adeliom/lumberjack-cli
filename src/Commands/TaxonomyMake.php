<?php


namespace Adeliom\WP\CLI\Commands;

use Adeliom\WP\CLI\Parser;
use Exception;
use ICanBoogie\Inflector;
use Jawira\CaseConverter\Convert;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class TaxonomyMake extends MakeFromStubCommand
{
    protected $signature = 'make:taxonomy {name : The class name of the Taxonomy (singular)}';

    protected $description = 'Create a Taxonomy';


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Create a new Taxonomy');

        $singular = $input->getArgument('name');
        $converter = new Convert($singular);
        $singular = $converter->toPascal();
        $plural   = Inflector::get('en')->pluralize($singular);
        $name     = Str::slug($singular);

        $helper = new QuestionHelper;

        $question = new Question('<info>Plural</info> [default: ' . $plural . '] ', $plural);
        $plural   = $helper->ask($input, $output, $question);

        $question = new Question('<info>WordPress Taxonomy Name</info> [default: ' . $name . '] ', $name);
        $name     = $helper->ask($input, $output, $question);

        $question         = new ConfirmationQuestion('<info>Register Taxonomy from Config? (y/n)</info> [default: y] ');
        $registerTaxonomy = $helper->ask($input, $output, $question);

        $stub = file_get_contents(__DIR__ . '/stubs/Taxonomy.stub');
        $stub = str_replace('DummyTaxonomy', $singular, $stub);
        $stub = str_replace('dummy-taxonomy', $name, $stub);
        $stub = str_replace('DummyPlural', $plural, $stub);

        try {
            $this->createFile('app/Taxonomy/' . $singular . '.php', $stub);
            $io->success('The Taxonomy "' . $singular . '" was created in file ' . 'app/Taxonomy/' . $singular . '.php');
            if ($registerTaxonomy) {
                $this->registerTaxonomyInConfig($singular);
                $io->success('The Taxonomy "' . $singular . '" was registred in config file : ' . $this->app->basePath() . '/config/taxonomies.php');
            }
            return 1;
        } catch (Exception $e) {
            $io->error($e->getMessage());
            return 0;
        }
    }

    protected function registerTaxonomyInConfig($singular)
    {
        $configPath = $this->app->basePath() . '/config/taxonomies.php';
        $config     = file_get_contents($configPath);
        $config     = str_replace("'register' => [", "'register' => [\n\t\tApp\Taxonomy\\" . $singular . "::class,", $config);
        file_put_contents($configPath, $config);
    }
}
