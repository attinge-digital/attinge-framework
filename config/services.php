<?php

use Attinge\Framework\Controller\AbstractController;
use Attinge\Framework\Dbal\ConnectionFactory;
use Doctrine\DBAL\Connection;
use League\Container\Argument\Literal\ArrayArgument;
use League\Container\Argument\Literal\StringArgument;
use League\Container\Container;
use League\Container\ReflectionContainer;
use Symfony\Component\Dotenv\Dotenv;
use Twig\Loader\FilesystemLoader;

$dotenv = new Dotenv();
$dotenv->load(BASE_PATH . '/.env');

$container = new Container();

$container->delegate(new ReflectionContainer(true));

# parameters for application config
$routes = include BASE_PATH . '/routes/web.php';
$appEnv = $_SERVER['APP_ENV'];
$templatesPath = BASE_PATH . '/resources/views/';

$container->add('APP_ENV', new StringArgument($appEnv));
$databaseUrl = 'sqlite:///' . BASE_PATH . '/var/db.sqlite';

# services

$container->add(
    Attinge\Framework\Routing\RouterInterface::class,
    Attinge\Framework\Routing\Router::class
);

$container->extend(Attinge\Framework\Routing\RouterInterface::class)
          ->addMethodCall(
              'setRoutes',
              [new ArrayArgument($routes)]
          )
;

$container->add(Attinge\Framework\Http\Kernel::class)
          ->addArgument(Attinge\Framework\Routing\RouterInterface::class)
          ->addArgument($container)
;

$container->addShared('filesystem-loader', FilesystemLoader::class)
          ->addArgument(new StringArgument($templatesPath))
;

$container->addShared('twig', \Twig\Environment::class)
          ->addArgument('filesystem-loader')
;

$container->add(AbstractController::class);

$container->inflector(AbstractController::class)
          ->invokeMethod('setContainer', [$container])
;

$container->add(\Attinge\Framework\Dbal\ConnectionFactory::class)
          ->addArguments([
              new \League\Container\Argument\Literal\StringArgument($databaseUrl)
          ]);

$container->addShared(\Doctrine\DBAL\Connection::class, function () use ($container): \Doctrine\DBAL\Connection {
    return $container->get(\Attinge\Framework\Dbal\ConnectionFactory::class)->create();
});

return $container;

