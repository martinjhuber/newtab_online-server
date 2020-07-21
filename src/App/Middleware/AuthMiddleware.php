<?php

namespace App\Middleware;

use App\Exceptions\NotAuthorizedException;
use App\DataLayer\UsersDataLayer;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class AuthMiddleware {

    protected $accountDataLayer;
    protected $tokenHeaderName;

    public function __construct (UsersDataLayer $usersDataLayer, ContainerInterface $container) {
        $this->usersDataLayer = $usersDataLayer;
        $this->tokenHeaderName = $container->get("config")["token.header"];
    }

    public function __invoke(Request $request, RequestHandler $handler)
    {

        $header = $request->getHeader($this->tokenHeaderName);
        if (!isset($header) || count($header) != 1 || strlen($header[0]) != 36) {
            throw new NotAuthorizedException("token_missing");
        }
        
        $token = $header[0];
        $user = $this->usersDataLayer->getUserByToken($token);
        if ($user === null) {
            throw new NotAuthorizedException("token_invalid");
        }

        $request = $request->withAttribute('user', $user);
        $request = $request->withAttribute('loginToken', $token);
        return $handler->handle($request);

    }

}

?>