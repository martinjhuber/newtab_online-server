<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\DataLayer\GridDefinitionDataLayer;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class GridController extends Controller {

    protected $gridDefDataLayer;

    public function __construct (GridDefinitionDataLayer $gridDefDataLayer) {
        $this->gridDefDataLayer = $gridDefDataLayer;
    }

    public function getGridDefinition(Request $request, Response $response, $args) {

        $user = $request->getAttribute('user');
        $gridDefinition = $this->gridDefDataLayer->getGridDefinition($user->id);
        return $this->respondJson($response, $gridDefinition->toJson());
        
    }

    public function getGridDefinitionVersion(Request $request, Response $response, $args) {

        $user = $request->getAttribute('user');
        $gdv = $this->gridDefDataLayer->getGridDefinitionVersion($user->id);
        return $this->respondJson($response, $gdv->toJson());
        
    }

}

?>