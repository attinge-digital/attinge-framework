<?php

namespace Attinge\Framework\Http;

readonly class Response {
	public function __construct(
		public ?string $content = '',
		public int     $status = 200,
		public array   $headers = [],
	) {
		http_response_code($this->status);
	}

	public function send() : void {
		echo $this->content;
	}
}