<?php

namespace Attinge\Framework\Console;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class Application
{
    public function __construct(
        private readonly ContainerInterface $container,
    ) {}
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ConsoleException
     */
    public function run() : int
    {
        $argv        = $_SERVER['argv'];
        $commandName = $argv[1] ?? null;

        if (!$commandName) {
            throw new ConsoleException('A command name must be provided');
        }

        $args = array_slice($argv, 2);
        $options = $this->parseOptions($args);

        return $this->container->get($commandName)?->execute($options);
    }

    private function parseOptions(array $args) : array
    {
        $options = [];

        foreach ($args as $arg) {
            if (str_starts_with($arg, '--')) {
                $option              = explode('=', substr($arg, 2));
                $options[$option[0]] = $option[1] ?? true;
            }
        }

        return $options;
    }
}