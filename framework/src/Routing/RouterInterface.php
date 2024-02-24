<?php

namespace Attinge\Framework\Routing;

use Attinge\Framework\Http\Request;
use Psr\Container\ContainerInterface;

interface RouterInterface
{
    public function dispatch(Request $request, ContainerInterface $container) : array;
    public function setRoutes(array $routes) : void;
}