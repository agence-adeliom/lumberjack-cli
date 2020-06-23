# Adeliom LumberJack CLI

This is where your description should go. Try and limit it to a paragraph or two, and maybe throw in a mention of what PSRs you support to avoid any confusion with users and contributors.

## Installation

You can install the package via composer:

```bash
composer config repositories.adeliom-wp-cli vcs https://adeliom:93ba9634b50f6531edf61140fba54fe1d7e93fc4@github.com/adeliom/wp-cli
composer require adeliom/wp-cli
```

Once installed you need to copy the `console` file into your Lumberjack theme directory.

It is assuming you're using Lumberjack inside Bedrock. If not, you may need to make some changes to paths in the `console` file

## Basic Usage
You can now access the Adeliom CLI from inside your Lumberjack theme directory:


### Available commands

```
  help                    Displays help for a command
  list                    Lists commands
 make
  make:admin              Create a Admin
  make:ajax               Create a Ajax Action
  make:controller         Create a Controller
  make:cron               Create a Cron Job
  make:env                Create a environement
  make:event              Create a Event Listener
  make:exception          Create a Exception
  make:flex-layout        Create a FlexibleLayout
  make:form               Create a Form
  make:hook               Create a Hooks Class
  make:posttype           Create a PostType
  make:provider           Create a ServiceProvider
  make:taxonomy           Create a Taxonomy
  make:viewmodel          Create a ViewModel
 route
  route:list              List all registered routes
```

### To show available commands

```
php console list
```

### To run a command
For a given command called `test:command` you would run the following:

```
php console test:command
```

### Get additional help about a command
For a given command called `test:command` you would run the following:

```
php console help test:command
```


## Adding Commands
To add additional commands to Adeliom CLI add them to `config/commands.php` (create the file if it doesn't exist).

```php
// config/commands.php

return [
    'commands' => [
        MyCommand::class,
    ],
];
```

## Writing Commands
Create a subclass of `Adeliom\WP\CLI\Commands\Command`:

```php
namespace MyNamespace;

use Adeliom\WP\CLI\Commands\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ControllerMake extends Command
{
    protected $signature = 'test:command {paramName : The description of the parameter}';

    protected $description = 'A description of the command';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Command implementation
    }
}
```

Adeliom CLI uses the same `$signature` syntax as Laravel, [see here](https://laravel.com/docs/5.6/artisan#writing-commands) for more information.

Adeliom CLI `Command` is a subclass of Symfony's `Command` object, for more information on how to implement the `execute()` function [see here](https://symfony.com/doc/current/console.html).

## Testing

``` bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Adeliom](https://github.com/adeliom)
- [Arnaud Ritti](https://github.com/aritti)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
