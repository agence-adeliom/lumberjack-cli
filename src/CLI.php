<?php

namespace Adeliom\WP\CLI;

use Adeliom\WP\CLI\Commands\AdminMake;
use Adeliom\WP\CLI\Commands\BlockMake;
use Adeliom\WP\CLI\Commands\CommandMake;
use Adeliom\WP\CLI\Commands\ControllerMake;
use Adeliom\WP\CLI\Commands\CronMake;
use Adeliom\WP\CLI\Commands\EnvMake;
use Adeliom\WP\CLI\Commands\EventMake;
use Adeliom\WP\CLI\Commands\ExceptionMake;
use Adeliom\WP\CLI\Commands\FormMake;
use Adeliom\WP\CLI\Commands\HookMake;
use Adeliom\WP\CLI\Commands\LayoutMake;
use Adeliom\WP\CLI\Commands\PostTypeMake;
use Adeliom\WP\CLI\Commands\RouteList;
use Adeliom\WP\CLI\Commands\ServiceProviderMake;
use Adeliom\WP\CLI\Commands\TaxonomyMake;
use Adeliom\WP\CLI\Commands\TwigExtensionMake;
use Adeliom\WP\CLI\Commands\ViewModelMake;
use Rareloop\Lumberjack\Application;
use Rareloop\Lumberjack\Bootstrappers\BootProviders;
use Rareloop\Lumberjack\Bootstrappers\LoadConfiguration;
use Rareloop\Lumberjack\Bootstrappers\RegisterExceptionHandler;
use Rareloop\Lumberjack\Bootstrappers\RegisterFacades;
use Rareloop\Lumberjack\Bootstrappers\RegisterProviders;
use Symfony\Component\Console\Application as ConsoleApplication;
use Rareloop\Lumberjack\Config;

class CLI
{
    protected $bootstrappers = [
        RegisterExceptionHandler::class,
        LoadConfiguration::class,
        RegisterFacades::class,
        RegisterProviders::class,
        BootProviders::class,
        RegisterCommands::class,
    ];
    protected $defaultCommands = [
        ControllerMake::class,
        ExceptionMake::class,
        ServiceProviderMake::class,
        ViewModelMake::class,
        PostTypeMake::class,
        TaxonomyMake::class,
        BlockMake::class,
        CronMake::class,
        RouteList::class,
        FormMake::class,
        EnvMake::class,
        HookMake::class,
        EventMake::class,
        AdminMake::class,
        LayoutMake::class,
        TwigExtensionMake::class,
        CommandMake::class
    ];
    private $app;

    public function __construct(Application $app)
    {
        $this->app        = $app;
        $this->consoleApp = $this->app->make(ConsoleApplication::class, ['name' => 'Adeliom WP - Lumberjack CLI']);

        $this->app->bind(WP::class, $this);
    }

    public function bootstrap()
    {
        $this->loadDefaultCommands();
        $this->app->bootstrapWith($this->bootstrappers());
    }

    protected function loadDefaultCommands()
    {
        foreach ($this->defaultCommands() as $command) {
            $this->consoleApp->add($this->app->make($command));
        }

        if (class_exists('\Adeliom\WP\Extensions\Extensions')) {
            $this->consoleApp->add($this->app->make('\Adeliom\WP\Extensions\Commands\PublishConfigs'));
        }

        $config  = $this->app->get(Config::class);
        $commands = $config->get('commands', []);

        foreach ($commands as $command) {
            $this->consoleApp->add($this->app->make($command));
        }
    
    }

    public function defaultCommands()
    {
        return $this->defaultCommands;
    }

    protected function bootstrappers()
    {
        return $this->bootstrappers;
    }

    public function run()
    {
        $this->consoleApp->run();
    }

    public function console()
    {
        return $this->consoleApp;
    }
}
