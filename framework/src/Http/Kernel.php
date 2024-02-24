<?php

namespace Attinge\Framework\Http;

use Attinge\Framework\Http\Exception\HttpException;
use Attinge\Framework\Routing\RouterInterface;
use Doctrine\DBAL\Connection;
use Exception;
use Psr\Container\ContainerInterface;

class Kernel
{
    private string $appEnv;
    public function __construct(
        private readonly RouterInterface $router,
        private readonly ContainerInterface $container,
    ) {
        $this->appEnv = $this->container->get('APP_ENV');
    }
    public function handle(Request $request) : Response
    {
        try {
            [$routeHandler, $vars] = $this->router->dispatch($request, $this->container);
            $response = call_user_func_array($routeHandler, $vars);
        }catch (Exception $exception) {
            $response = $this->createExceptionResponse($exception);
        }

        return $response;
    }
    /**
     * @throws Exception
     */
    private function createExceptionResponse(Exception $exception) : Response
    {
        if (in_array($this->appEnv, ['dev', 'test'])) {
            throw $exception;
        }

        if ($exception instanceof HttpException) {
            return new Response($exception->getMessage(), $exception->getStatusCode());
        }

        return new Response('Server error', Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
