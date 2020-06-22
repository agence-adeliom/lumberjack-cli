<?php


namespace Adeliom\WP\CLI;

use Rareloop\Lumberjack\Application;
use Rareloop\Lumberjack\Config;

class RegisterCommands
{
    public function bootstrap(Application $app)
    {
        $config = $app->get(Config::class);
        $hatchet = $app->get(CLI::class);

        $commands = $config->get('commands', []);

        foreach ($commands as $command) {
            $hatchet->console()->add($app->make($command));
        }
    }
}
