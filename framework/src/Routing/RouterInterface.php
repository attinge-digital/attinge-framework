<?php

namespace Attinge\Framework\Routing;

use Attinge\Framework\Http\Request;

interface RouterInterface {
	public function dispatch(Request $request);
}