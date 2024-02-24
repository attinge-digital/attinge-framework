<?php

namespace Attinge\Framework\Routing;

use Attinge\Framework\Http\Exception\HttpException;
use Attinge\Framework\Http\Exception\HttpRequestMethodException;
use Attinge\Framework\Http\Request;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Psr\Container\ContainerInterface;

use function FastRoute\simpleDispatcher;

class Router implements RouterInterface
{
    private array $routes;
    /**
     * @throws HttpRequestMethodException
     * @throws HttpException
     */
    public function dispatch(Request $request, ContainerInterface $container) : array
    {
        $routeInfo = $this->extractRouteInfo($request);
        [$handle, $vars] = $routeInfo;

        if (is_array($handle)) {
            [$controllerId, $method] = $handle;
            $controller = $container->get($controllerId);
            $handle     = [$controller, $method];
        }

        return [$handle, $vars];
    }

    public function setRoutes(array $routes) : void
    {
        $this->routes = $routes;
    }

    /**
     * @throws HttpRequestMethodException
     * @throws HttpException
     */
    private function extractRouteInfo(Request $request) : array
    {
        $dispatcher = simpleDispatcher(function (RouteCollector $routeCollector) {
            foreach ($this->routes as $route) {
                if (!($route instanceof Route)) {
                    continue;
                }
                $routeCollector->addRoute($route->method, $route->pattern, $route->handler);
            }
        });

        $routeInfo = $dispatcher->dispatch(
            $request->getMethod(),
            $request->getPathInfo(),
        );

        switch ($routeInfo[0]) {
            case Dispatcher::FOUND:
                return [$routeInfo[1], $routeInfo[2]];
            case Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = implode(', ', $routeInfo[1]);
                $e              = new HttpRequestMethodException("The allowed methods are $allowedMethods");
                $e->setStatusCode(405);
                throw $e;
            default:
                $e = new HttpException('Route not found');
                $e->setStatusCode(404);
                throw $e;
        }
    }
}