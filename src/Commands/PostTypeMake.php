<?php


namespace Adeliom\WP\CLI\Commands;

use Adeliom\WP\CLI\Parser;
use Exception;
use ICanBoogie\Inflector;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Jawira\CaseConverter\Convert;

class PostTypeMake extends MakeFromStubCommand
{
    protected $signature = 'make:posttype {name : The class name of the PostType (singular)}';

    protected $description = 'Create a PostType';


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Create a new PostType');

        $singular = $input->getArgument('name');
        $converter = new Convert($singular);
        $singular = $converter->toPascal();

        $plural   = Inflector::get('en')->pluralize($singular);
        $name     = $converter->toKebab();
        $slug     = $converter->toKebab();

        $helper = new QuestionHelper;

        $question = new Question('<info>Plural</info> [default: ' . $plural . '] ', $plural);
        $plural   = $helper->ask($input, $output, $question);

        $question = new Question('<info>WordPress Post Name</info> [default: ' . $name . '] ', $name);
        $name     = $helper->ask($input, $output, $question);

        $question = new Question('<info>Slug</info> [default: ' . $slug . '] ', $slug);
        $slug     = $helper->ask($input, $output, $question);

        $features = [
            'Content Editor',
            'Featured Images',
            'Revisions',
            'Archives',
        ];
        $question = new ChoiceQuestion(
            '<info>Which features do you want?</info> [default: 0,1,2,3]',
            $features,
            '0,1,2,3'
        );
        $question->setMultiselect(true);
        $featuresSelected = $helper->ask($input, $output, $question);

        $question         = new ConfirmationQuestion('<info>Register PostType from Config? (y/n)</info> [default: y] ');
        $registerPostType = $helper->ask($input, $output, $question);

        $stub = file_get_contents(__DIR__ . '/stubs/PostType.stub');
        $stub = str_replace('DummyPostType', $singular, $stub);
        $stub = str_replace('dummy-post-name', $name, $stub);
        $stub = str_replace('dummy-slug', $slug, $stub);
        $stub = str_replace('DummyPlural', $plural, $stub);

        if (!in_array('Archives', $featuresSelected)) {
            $stub = str_replace("'has_archive' => true,", "'has_archive' => false,", $stub);
        }

        if (!in_array('Content Editor', $featuresSelected)) {
            $stub = str_replace("'editor',\n", '', $stub);
        }

        if (!in_array('Revisions', $featuresSelected)) {
            $stub = str_replace("'revisions',\n", '', $stub);
        }

        if (!in_array('Featured Images', $featuresSelected)) {
            $stub = str_replace("'thumbnail',\n", '', $stub);
        }

        try {
            $this->createFile('app/PostTypes/' . $singular . '.php', $stub);
            $io->success('The PostTypes "' . $singular . '" was created in file ' . 'app/PostTypes/' . $singular . '.php');
            if ($registerPostType) {
                $this->registerPostTypeInConfig($singular);
                $io->success('The PostTypes "' . $singular . '" was registred in config file : ' . $this->app->basePath() . '/config/posttypes.php');
            }
            return 1;
        } catch (Exception $e) {
            $io->error($e->getMessage());
            return 0;
        }
    }

    protected function registerPostTypeInConfig($singular)
    {
        $configPath = $this->app->basePath() . '/config/posttypes.php';
        $config     = file_get_contents($configPath);
        $config     = str_replace("'register' => [", "'register' => [\n\t\tApp\PostTypes\\" . $singular . "::class,", $config);
        file_put_contents($configPath, $config);
    }
}
