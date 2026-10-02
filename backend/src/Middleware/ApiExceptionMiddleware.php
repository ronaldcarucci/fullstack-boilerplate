<?php
namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Cake\Http\Response;
use Cake\Core\Configure;

class ApiExceptionMiddleware implements MiddlewareInterface
{
    /**
     * @throws \Throwable
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (\Throwable $exception) {
            if (!str_starts_with($request->getUri()->getPath(), '/api')) {
                throw $exception;
            }

            $code = $exception->getCode();
            if ($code < 100 || $code > 599) {
                $code = 500;
            }

            $response = new Response();
            $responseData = [
                'error' => true,
                'message' => $exception->getMessage(),
                'code' => $code
            ];

            if (Configure::read('debug')) {
                $responseData['trace'] = explode("\n", $exception->getTraceAsString());
            }

            return $response
                ->withStatus($code)
                ->withType('application/json')
                ->withStringBody(json_encode($responseData));
        }
    }
}
