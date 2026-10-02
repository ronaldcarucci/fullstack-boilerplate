<?php
namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Cake\Http\Response;
use Cake\Http\ServerRequest;

class CorsMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var ServerRequest $cakeRequest */
        $cakeRequest = $request;

        if ($request->getMethod() === 'OPTIONS') {
            $response = new Response();
            return $response->cors($cakeRequest)
            ->allowOrigin(['*'])
                ->allowMethods(['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'])
                ->allowHeaders(['Content-Type', 'Authorization', 'X-Requested-With'])
                ->maxAge(86400)
                ->build();
        }

        $response = $handler->handle($request);

        /** @var Response $cakeResponse */
        $cakeResponse = $response;

        return $cakeResponse->cors($cakeRequest)
            ->allowOrigin(['*'])
            ->allowMethods(['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'])
            ->allowHeaders(['Content-Type', 'Authorization', 'X-Requested-With'])
            ->build();
    }
}
