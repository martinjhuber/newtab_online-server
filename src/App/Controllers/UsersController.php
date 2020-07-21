<?php

namespace App\Controllers;

use App\Exceptions\NotFoundException;
use App\Helpers\RequestVerifier;
use App\Controllers\Controller;
use App\DataLayer\UsersDataLayer;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class UsersController extends Controller {

    protected $rv;
    protected $usersDataLayer;

    public function __construct (UsersDataLayer $usersDataLayer, RequestVerifier $rv) {
        $this->usersDataLayer = $usersDataLayer;
        $this->rv = $rv;
    }

    // AUTH: None
    public function login(Request $request, Response $response, $args) {

        $body = $request->getParsedBody();

        $this->rv->isContainer($body);
        $name = $this->rv->verifyString($body, "name", 1);
        $pw = $this->rv->verifyString($body, "password", 1);

        // TODO: Hash password
        $dbResult = $this->usersDataLayer->login($name, $pw);

        if ($dbResult === null || !isset($dbResult[0])) {
            throw new NotFoundException("user_unknown");
        }

        return $this->respondJson($response, array_merge($dbResult[0]->toJson(), $dbResult[1]->toJson()));

    }

    // AUTH: None
    public function getUserByToken(Request $request, Response $response, $args) {

        $body = $request->getParsedBody();

        $this->rv->isContainer($body);
        $token = $this->rv->verifyString($body, "token", 36, 36);

        $user = $this->usersDataLayer->getUserByToken($token);
        if ($user === null) {
            throw new NotFoundException("token_invalid");
        }
        return $this->respondJson($response, $user->toJson());

    }

    // AUTH: Required
    public function getUser(Request $request, Response $response, $args) {

        $user = $request->getAttribute('user');
        return $this->respondJson($response, $user->toJson());

    }

    // AUTH: Required
    public function logout(Request $request, Response $response, $args) {

        $loginToken = $request->getAttribute('loginToken');

        $dbResult = $this->usersDataLayer->logout($loginToken);

        return $this->respondJson($response, []);

    }


}

?>