<?php

namespace Attinge\Framework\Routing;

use Attinge\Framework\Http\Exception\HttpException;
use Attinge\Framework\Http\Exception\HttpRequestMethodException;
use Attinge\Framework\Http\Request;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

class Router implements RouterInterface {

	public function dispatch(Request $request) : array {
		$routeInfo = $this->extractRouteInfo($request);
		[$handle, $vars] = $routeInfo;

		if (is_array($handle)) {
			[$controller, $method] = $handle;
			$handle = [new $controller(), $method];
		}

		return [$handle, $vars];
	}

	/**
	 * @throws HttpRequestMethodException
	 * @throws HttpException
	 */
	private function extractRouteInfo(Request $request) {
		$dispatcher = simpleDispatcher(function(RouteCollector $routeCollector) {
			$routes = include BASE_PATH . '/routes/web.php';
			foreach ($routes as $route) {
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
				throw new HttpRequestMethodException("The allowed methods are $allowedMethods");
			default:
				throw new HttpException('Route not found');
		}
	}
}