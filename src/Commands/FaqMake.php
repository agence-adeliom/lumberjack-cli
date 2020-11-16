<?php

namespace App\Commands;

use Exception;
use Adeliom\WP\CLI\Commands\MakeFromStubCommand;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class FaqMake extends MakeFromStubCommand
{
    protected $signature = 'faq:generate';

    protected $description = 'Generate FAQ';

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        try {

            $postType = "Faq";

            $slug = "faq";

            $adminName = "FaqAdmin";

            /* SCSS NAME */
            $singleNameCss = "single-faq";
            $listingNameCss = "list-faqs";

            /* SCSS ClassName */
            $singleClassName = "faq";
            $listingClassName = "list-faqs";

            /* TWIG NAME */
            $singleNameTwig = "single-faq";
            $listingNameTwig = "list-faqs";

            /* TPL PHP */
            $singleNameTpl = "single-faq";
            $listingNameTpl = "tpl-listing-faq";

            /* TWIG NAME FLEX */
            $flexFileName = "flex-faq";

            $io = new SymfonyStyle($input, $output);

            /* CREATE POST TYPE CLASS */
            $io->title('Create a new PostType');

            $helper = new QuestionHelper();

            $question = new Question('<info>Rewrite slug</info> [default: ' . $slug . '] ', $slug);
            $slug = $helper->ask($input, $output, $question);

            $question = new ConfirmationQuestion('<info>FAQ has category (taxonomy) ?</info> [default: n] ');
            $hasTaxonomy = $helper->ask($input, $output, $question);

            $question = new ConfirmationQuestion('<info>Faq has listing page ?</info> [default: y] ');
            $listingPage = $helper->ask($input, $output, $question);

            $createListingPage = false;
            if($listingPage) {
                $question = new ConfirmationQuestion('<info>Create this listing page (BO) ?</info> [default: y] ');
                $createListingPage = $helper->ask($input, $output, $question);
            }

            $question = new ConfirmationQuestion('<info>Faq has details page ?</info> [default: y] ');
            $detailsPage = $helper->ask($input, $output, $question);

            $question = new ConfirmationQuestion('<info>Create FAQ Flex Content ?</info> [default: y] ');
            $createFlexContent = $helper->ask($input, $output, $question);

            $stubPostType = file_get_contents(__DIR__ . '/stubs/Faq/FaqPostType.stub');
            $stubPostType = str_replace('dummy-slug', $slug, $stubPostType);
            $stubPostType = str_replace('dummy-page', $detailsPage, $stubPostType);

            $this->createFile('app/PostTypes/' . $postType . '.php', $stubPostType);
            $io->success('The PostTypes "' . $postType . '" was created in file ' . 'app/PostTypes/' . $postType . '.php');

            $this->registerPostTypeInConfig($postType);
            $io->success('The PostTypes "' . $postType . '" was registred in config file : ' . $this->app->basePath() . '/config/posttypes.php');
            /* END POST TYPE CLASS */


            /* CREATE TAXONOMY CLASS */
            if($hasTaxonomy){
                $io->title('Create a new Taxonomy class');
                $stubTaxonomy = file_get_contents(__DIR__ . '/stubs/Faq/FaqPostType.stub');
                $stubTaxonomy = str_replace('dummyPost', $postType, $stubTaxonomy);
                $this->createFile('app/Taxonomy/' . $postType . '/Category.php', $stubTaxonomy);
            }


            /* CREATE ADMIN CLASS */
            $io->title('Create a new Admin class');

            if($detailsPage){
                $stubAdmin = file_get_contents(__DIR__ . '/stubs/Faq/FaqAdmin.stub');
            }
            else{
                $stubAdmin = file_get_contents(__DIR__ . '/stubs/Faq/FaqSimpleAdmin.stub');
            }

            $this->createFile('app/Admin/' . $adminName . '.php', $stubAdmin);
            $io->success('The Admin class "' . $adminName . '" was created in file ' . 'app/Admin/' . $adminName . '.php');
            /* END ADMIN CLASS */


            /* CREATE LISTING PAGES */
            if($listingPage) {

                $io->title('Create a listing TPL');
                $stubListingTpl = file_get_contents(__DIR__ . '/stubs/Faq/FaqListingTpl.stub');
                $stubListingTpl = str_replace('dummy-file', $listingNameTwig, $stubListingTpl);

                $this->createFile($listingNameTpl . '.php', $stubListingTpl);
                $io->success('The listing tpl "' . $listingNameTpl . '" was created in file /' . $stubListingTpl . '.php');


                $io->title('Create a new Twig view');
                $stubListingView = file_get_contents(__DIR__ . '/stubs/Faq/FaqListingTwig.stub');
                $stubListingView = str_replace('dummy-file', $listingNameCss, $stubListingView);

                $this->createFile('views/templates/faq/' . $listingNameTwig . '.html.twig', $stubListingView);
                $io->success('The listing view "' . $listingNameTwig . '" was created in file ' . 'views/templates/faq/' . $listingNameTwig . '.html.twig');


                $io->title('Create a new Scss');
                $stubListingCss = file_get_contents(__DIR__ . '/stubs/Faq/ScssPage.stub');
                $stubListingCss = str_replace('dummy-file', $listingNameCss, $stubListingCss);
                $stubListingCss = str_replace('dummy-classname', $listingClassName, $stubListingCss);

                $this->createFile('assets/styles/pages/faq/' . $listingNameCss . '.scss', $stubListingCss);
                $io->success('The listing page style "' . $listingNameCss . '" was created in file ' . 'assets/styles/pages/faq/' . $listingNameCss . '.scss');

                if($createListingPage){

                    $post_data = array(
                        'post_title'    => wp_strip_all_tags("FAQ"),
                        'post_name' => $slug,
                        'post_content'  => "",
                        'post_status'   => "publish",
                        'post_type'     => "page",
                        'page_template' => $listingNameTpl . '.php'
                    );

                    wp_insert_post($post_data);

                }

            }
            /* END LISTING PAGES */


            /* CREATE SINGLE PAGES */
            if($detailsPage) {

                $io->title('Create a single TPL');
                $stubSingleTpl = file_get_contents(__DIR__ . '/stubsFaq/FaqSingleTpl.stub');
                $stubSingleTpl = str_replace('dummy-file', $singleNameTwig, $stubSingleTpl);

                $this->createFile($singleNameTpl . '.php', $stubSingleTpl);
                $io->success('The single tpl "' . $singleNameTpl . '" was created in file /' . $singleNameTpl . '.php');


                $io->title('Create a new Twig view');
                $stubSingleView = file_get_contents(__DIR__ . '/stubs/Faq/FaqSingleTwig.stub');
                $stubSingleView = str_replace('dummy-file', $singleNameCss, $stubSingleView);

                $this->createFile('views/templates/faq/' . $singleNameTwig . '.html.twig', $stubSingleView);
                $io->success('The single view "' . $singleNameTwig . '" was created in file ' . 'views/templates/faq/' . $singleNameTwig . '.html.twig');


                $io->title('Create a new Scss');
                $stubSingleCss = file_get_contents(__DIR__ . '/stubs/Faq/ScssPage.stub');
                $stubSingleCss = str_replace('dummy-file', $singleNameCss, $stubSingleCss);
                $stubSingleCss = str_replace('dummy-classname', $singleClassName, $stubSingleCss);

                $this->createFile('assets/styles/pages/faq/' . $singleNameCss . '.scss', $stubSingleCss);
                $io->success('The single page style "' . $singleNameCss . '" was created in file ' . 'assets/styles/pages/faq/' . $singleNameCss . '.scss');

            }
            /* END SINGLE PAGES */

            if($createFlexContent) {

                $io->title('Create a new PHP layout');
                $stubLayoutPhp = file_get_contents(__DIR__ . '/stubs/Faq/FaqFlexPhp.stub');
                $stubLayoutPhp = str_replace('DummyLayout', $postType .'Layout', $stubLayoutPhp);

                $this->createFile('app/FlexibleLayout/' . $postType . 'Layout.php', $stubLayoutPhp);
                $io->success('The Layout  "' . $postType .'Layout' . '" was created in file ' . 'app/FlexibleLayout/' . $postType .'Layout.php');

                $io->title('Create a new Twig Extension');
                $stubTwigExtension = file_get_contents(__DIR__ . '/stubs/Faq/FaqTwigExtension.stub');
                $stubTwigExtension = str_replace('DummyLayout', $postType .'Extension', $stubTwigExtension);

                $this->createFile('app/TwigExtensions/' . $postType .'Extension.php', $stubTwigExtension);
                $io->success('The twig extension "' . $postType . 'Extension.php" was created in file ' . 'app/TwigExtensions/' . $flexFileName . 'Extension.php');


                $io->title('Create a new Twig');
                $stubFlexTwig = file_get_contents(__DIR__ . '/stubs/Faq/FaqFlexTwig.stub');
                $this->createFile('views/flexible-content/' . $flexFileName . '.html.twig', $stubFlexTwig);
                $io->success('The twig view "' . $flexFileName . '" was created in file ' . 'views/flexible-content/' . $flexFileName . '.html.twig');


                $io->title('Create a new Scss');
                $this->createFile('assets/styles/components/flexible-contents/includes/_' . $singleClassName . '.scss', '// assets/styles/components/flexible-contents/includes/_' . $key . '.scss');
                $io->success('The css style "' . $singleClassName . '" was created in file ' . 'assets/styles/components/flexible-contents/includes/_' . $singleClassName . '.scss');

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
