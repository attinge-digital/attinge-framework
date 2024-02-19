<?php

namespace Attinge\Framework\Http;

use Attinge\Framework\Http\Exception\HttpException;
use Attinge\Framework\Routing\Router;

class Kernel {
	public function __construct(
		private readonly Router $router,
	) {}
	public function handle(Request $request) : Response {
		try {
			[$routeHandler, $vars] = $this->router->dispatch($request);
			$response = call_user_func_array($routeHandler, $vars);
		} catch (HttpException $e) {
			$response = new Response($e->getMessage(), $e->getStatusCode());
		} catch (\Exception $e) {
			$response = new Response($e->getMessage(), 500);
		}

		return $response;

	}
}
