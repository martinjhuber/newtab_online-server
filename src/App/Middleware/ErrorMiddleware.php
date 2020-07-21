<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use App\Exceptions\BadRequestException;
use App\Exceptions\NotFoundException;
use App\Exceptions\NotAuthorizedException;
use Psr\Container\ContainerInterface;

class ErrorMiddleware {

    private $DEBUG;

    public function __construct(ContainerInterface $container) {
        $this->DEBUG = $container->get("config")["app.debugMode"];
    }

    public function __invoke(Request $request, RequestHandler $handler)
    {
        try {
            $response = $handler->handle($request);
            return $response;
        } catch (BadRequestException $ex) {
            return $this->createResponse($ex->getCode(), $this->createBody($ex->getMessage(), $ex->details));
        } catch (NotFoundException $ex) {
            return $this->createResponse($ex->getCode(), $this->createBody($ex->getMessage()));
        } catch (NotAuthorizedException $ex) {
            return $this->createResponse($ex->getCode(), $this->createBody($ex->getMessage()));
        } catch (\Exception $ex) {
            if ($this->DEBUG) {
                throw $ex;
            } else {
                return $this->createResponse(500, $this->createBody($ex->getMessage()));
            }
        }

    }

    private function createBody($message, $details = null) {
        $obj = ["error" => $message];
        if (isset($details)) {
            $obj["details"] = $details;
        }
        return json_encode($obj);
    }

    private function createResponse($statusCode, $payload) {
        $response = new Response();
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);
    }

}

?>