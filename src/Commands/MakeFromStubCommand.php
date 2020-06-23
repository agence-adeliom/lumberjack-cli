<?php


namespace Adeliom\WP\CLI\Commands;

abstract class MakeFromStubCommand extends Command
{
    protected function createFile($relativePath, $contents, $force = true)
    {
        $absolutePath = $this->app->basePath() . '/' . $relativePath;
        $directory    = dirname($absolutePath);

        if (!is_dir($directory)) {
            mkdir($directory, 0754, true);
        }
        if (!$force && file_exists($absolutePath)) {
            return 0;
        }
        file_put_contents($absolutePath, $contents);
        return 1;
    }
}
