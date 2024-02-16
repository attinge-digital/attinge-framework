<?php

namespace Attinge\Framework\Routing;

readonly class Route {
	public function __construct(
		public string  $method,
		public string  $pattern,
		public mixed   $handler,
		public ?string $middleware = null,
	) {}
}