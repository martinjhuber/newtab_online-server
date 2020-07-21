<?php

namespace App\Controllers;

//use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

abstract class Controller {

    protected function respondJson(Response $response, $data, $statusCode = 200) {

        $payload = json_encode($data);
        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);

    }

}

?>