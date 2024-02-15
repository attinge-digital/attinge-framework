<?php

namespace App\Controller;

use Attinge\Framework\Http\Response;

class PostsController {
	public function show(int $id) : Response {
		$content = "POST ID: $id";

		return new Response($content);
	}
}