<?php

namespace Adeliom\WP\CLI\Exceptions;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Rareloop\Lumberjack\Exceptions\Handler as LumberjackHandler;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Output\ConsoleOutput;
use Zend\Diactoros\Response\EmptyResponse;

class Handler extends LumberjackHandler
{
    protected $dontReport = [];

    public function report(Exception $e)
    {
        parent::report($e);
    }

    public function render(ServerRequestInterface $request, Exception $e): ResponseInterface
    {
        (new ConsoleApplication)->renderException($e, new ConsoleOutput);

        // Not ideal :(
        return new EmptyResponse();
    }
}
