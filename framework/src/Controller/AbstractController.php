<?php

namespace Attinge\Framework\Controller;

use Attinge\Framework\Http\Response;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

abstract class AbstractController
{
    protected ?ContainerInterface $container = null;

    public function setContainer(ContainerInterface $container) : void
    {
        $this->container = $container;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function render(string $template, array $parameters = [], Response $response = null) : Response
    {
        $content  = $this->container->get('twig')?->render($template, $parameters);
        $response ??= new Response();
        $response->setContent($content);

        return $response;
    }
}