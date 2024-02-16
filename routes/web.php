<?php

use App\Controller\HomeController;
use App\Controller\PostsController;
use Attinge\Framework\Http\Response;
use Attinge\Framework\Routing\Route;

return [
	new Route('GET', '/', [HomeController::class, 'index']),
	new Route('GET', '/posts/{id:\d+}', [PostsController::class, 'show']),
	new Route('*', '/hello/{name:.+}', function(string $name) {
		return new Response("Hello $name");
	}),
];