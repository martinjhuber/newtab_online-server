<?php
use DI\Container;
use Slim\Factory\AppFactory;
use App\Middleware\ErrorMiddleware;

require __DIR__.'/../vendor/autoload.php';

$container = new Container();
AppFactory::setContainer($container);
$app = AppFactory::create();

$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

require_once __DIR__.'/../App/load.php';

$config = require_once __DIR__.'/../Config/config.php';
$container->set('config', $config);

//$app->add(new ErrorMiddleware());
$app->add($container->get(ErrorMiddleware::class));
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$routes = require_once __DIR__.'/../App/routes.php';
$routes($app, $container, $config["api.basePath"]);

// Run app
$app->run();
?>