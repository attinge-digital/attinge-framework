<?php

namespace Attinge\Framework\Console;

use Attinge\Framework\Console\Command\CommandInterface;
use DirectoryIterator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionException;

final class Kernel
{
    public function __construct(
        private readonly ContainerInterface $container,
        private readonly Application $application,
    ) {}

    /**
     * @throws ReflectionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ConsoleException
     */
    public function handle() : int
    {
        $this->registerCommands();
        return $this->application->run();
    }
    /**
     * @throws ReflectionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function registerCommands() : void
    {
        $commandFiles = new DirectoryIterator(__DIR__ . '/Command');
        $namespace    = $this->container->get('base-commands-namespace');

        foreach ($commandFiles as $commandFile) {
            if (!$commandFile->isFile()) {
                continue;
            }

            $command = $namespace . pathinfo($commandFile, PATHINFO_FILENAME);

            if (is_subclass_of($command, CommandInterface::class)) {
                $commandName = (new \ReflectionClass($command))->getProperty('name')->getDefaultValue();
                $this->container->add($commandName, $command);
            }
        }
    }
}