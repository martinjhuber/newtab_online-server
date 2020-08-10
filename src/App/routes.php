<?php

use Slim\App;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Middleware\AuthMiddleware;
use App\Controllers\UsersController;
use App\Controllers\GridController;
use App\Controllers\TileController;

return function (App $app, $container, $basePath = "/") {

    $authMW = $container->get(AuthMiddleware::class);

    $app->get($basePath, function (Request $request, Response $response) {
        $response->getBody()->write('newtab-server API service v1');
        return $response;
    });

    $app->post($basePath.'user/login', UsersController::class . ':login');
    $app->post($basePath.'user/token/verify', UsersController::class . ':getUserByToken');
    $app->post($basePath.'user/logout', UsersController::class . ':logout')->add($authMW);
    $app->get($basePath.'user', UsersController::class . ':getUser')->add($authMW);   

    $app->get($basePath.'grid', GridController::class . ':getGridDefinition')->add($authMW);
    $app->get($basePath.'grid/version', GridController::class . ':getGridDefinitionVersion')->add($authMW);

    $app->post($basePath.'grid/tile', TileController::class . ':createTile')->add($authMW);
    $app->put($basePath.'grid/tile/{id}', TileController::class . ':editTile')->add($authMW);
    $app->delete($basePath.'grid/tile/{id}', TileController::class . ':deleteTile')->add($authMW);
    $app->put($basePath.'grid/tile/{id}/gridId', TileController::class . ':moveTileToGrid')->add($authMW);
    $app->put($basePath.'grid/tile/{id}/order', TileController::class . ':changeTileOrder')->add($authMW);
    $app->delete($basePath.'grid/tile/{id}/image', TileController::class . ':removeTileImage')->add($authMW);

}

?>