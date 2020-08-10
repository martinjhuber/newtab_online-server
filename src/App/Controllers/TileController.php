<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\DataLayer\TileDataLayer;
use App\Exceptions\BadRequestException;
use App\Exceptions\NotFoundException;
use App\Helpers\RequestVerifier;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class TileController extends Controller {

    protected $rv;
    protected $tileDataLayer;

    public function __construct (TileDataLayer $tileDataLayer, RequestVerifier $rv) {
        $this->tileDataLayer = $tileDataLayer;
        $this->rv = $rv;
    }

    // POST /grid/tile
    public function createTile(Request $request, Response $response, $args) {

        $userId = $request->getAttribute('user')->id;

        $body = $request->getParsedBody();

        $this->rv->isContainer($body);
        $text = $this->rv->verifyString($body, "text", 1);
        $href = $this->rv->verifyString($body, "href", 1);
        $color = $body["color"];
        $gridId = $this->rv->verifyInteger($body, "gridId", 0, 3);
        $w = $this->rv->verifyInteger($body, "w", 1, 2);
        $h = $this->rv->verifyInteger($body, "h", 1, 2);

        $result = $this->tileDataLayer->createTile($userId, $gridId, $w, $h, $text, $href, $color);

        if ($result === 1) {
            return $this->respondJson($response, null);
        } else {
            throw new BadRequestException("tile_create_failed");
        }

    }

    // PUT /grid/tile/$id
    public function editTile(Request $request, Response $response, $args) {

        $userId = $request->getAttribute('user')->id;
        $tileId = $args["id"];

        $body = $request->getParsedBody();

        $this->rv->isContainer($body);
        $text = $this->rv->verifyString($body, "text", 1);
        $href = $this->rv->verifyString($body, "href", 1);
        $color = $body["color"];
        $w = $this->rv->verifyInteger($body, "w", 1, 2);
        $h = $this->rv->verifyInteger($body, "h", 1, 2);

        $imageBase64Data = $body["imageBase64"];
        $imageScale = $body["imageScale"];

        if ($imageScale != null && ($imageScale < 0 || $imageScale > 200)) {
            $imageScale = null;
        }

        if ($imageBase64Data != null && substr($imageBase64Data, 0, 11) != "data:image/") {
            throw new BadRequestException("invalid_image", "Image data not formatted correctly.");
        }

        $result = $this->tileDataLayer->editTile($userId, $tileId, $w, $h, $text, $href, $color, $imageBase64Data, $imageScale);

        if ($result === 1) {
            return $this->respondJson($response, null);
        } else if ($result === -1) {
            throw new NotFoundException("tile_not_found");
        } else {
            throw new BadRequestException("tile_edit_failed");
        }

    }

    // DELETE /grid/tile/$id
    public function deleteTile(Request $request, Response $response, $args) {
        $userId = $request->getAttribute('user')->id;
        $tileId = $args["id"];

        $result = $this->tileDataLayer->deleteTile($userId, $tileId);

        if ($result === 1) {
            return $this->respondJson($response, null);
        } else {
            throw new BadRequestException("tile_not_found");
        }
    }

    // DELETE /grid/tile/$id/image
    public function removeTileImage(Request $request, Response $response, $args) {
        $userId = $request->getAttribute('user')->id;
        $tileId = $args["id"];

        $result = $this->tileDataLayer->removeTileImage($userId, $tileId);

        if ($result === 1) {
            return $this->respondJson($response, null);
        } else {
            throw new BadRequestException("tile_not_found");
        }
    }    

    // PUT /grid/tile/$id/gridId
    public function moveTileToGrid(Request $request, Response $response, $args) {
        $userId = $request->getAttribute('user')->id;
        $tileId = $args["id"];
        $body = $request->getParsedBody();
        $this->rv->isContainer($body);
        $gridId = $this->rv->verifyInteger($body, "gridId", 0, 3);

        $result = $this->tileDataLayer->moveTile($userId, $tileId, $gridId);

        if ($result === 1) {
            return $this->respondJson($response, null);
        } else {
            throw new BadRequestException("tile_edit_failed");
        }
    }

    // PUT /grid/tile/$id/order
    public function changeTileOrder(Request $request, Response $response, $args) {
        $userId = $request->getAttribute('user')->id;
        $tileId = $args["id"];
        $body = $request->getParsedBody();
        $this->rv->isContainer($body);
        $orderId = $this->rv->verifyInteger($body, "order", 0, 1000);

        $result = $this->tileDataLayer->reorderTile($userId, $tileId, $orderId);

        if ($result === 1) {
            return $this->respondJson($response, null);
        } else {
            throw new BadRequestException("tile_edit_failed");
        }
    }

}

?>