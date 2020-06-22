<?php

namespace Adeliom\WP\CLI\Commands;

use Adeliom\WP\CLI\Parser;
use Rareloop\Lumberjack\Application;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

abstract class Command extends SymfonyCommand
{
    protected $app;

    protected $name;

    protected $signature;

    protected $description;

    public function __construct(Application $app)
    {
        $this->app = $app;

        if (isset($this->signature)) {
            $this->configureFromSignature();
        } else {
            parent::__construct($this->name);
        }

        if (isset($this->description)) {
            $this->setDescription($this->description);
        }
    }

    protected function configureFromSignature()
    {
        list($name, $arguments, $options) = Parser::parse($this->signature);

        parent::__construct($this->name = $name);

        foreach ($arguments as $argument) {
            $this->getDefinition()->addArgument($argument);
        }

        foreach ($options as $option) {
            $this->getDefinition()->addOption($option);
        }
    }
}
