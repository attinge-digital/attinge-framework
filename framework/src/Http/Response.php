<?php

namespace Attinge\Framework\Http;

class Response
{
    final public const HTTP_INTERNAL_SERVER_ERROR = 500;
    public function __construct(
        public ?string $content = '',
        public int $status = 200,
        public array $headers = [],
    ) {
        http_response_code($this->status);
    }

    public function send() : void
    {
        echo $this->content;
    }
    public function setContent(?string $content) : void
    {
        $this->content = $content;
    }
}