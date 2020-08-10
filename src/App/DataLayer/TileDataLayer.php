<?php

namespace App\DataLayer;

use App\Database\SPCall;
use App\Database\Database;
use App\Database\DBDataType;

use App\Entities\Tile;

class TileDataLayer {

    protected $db;

    public function __construct (Database $db) {
        $this->db = $db;
    }

    public function createTile($userId, $gridId, $width, $height, $text, $href, $color) {
        
        $sp = new SPCall("tile_create");
        $sp->addInParam("user_id", $userId);
        $sp->addInParam("input_gridId", $gridId);
        $sp->addInParam("input_width", $width);
        $sp->addInParam("input_height", $height);
        $sp->addInParam("input_text", $text);
        $sp->addInParam("input_href", $href);
        $sp->addInParam("input_color", $color);
        $sp->addOutParam("result", DBDataType::IntType);

        $spResult = $this->db->executeSP($sp);
        return $spResult->out["result"];
    }


    public function editTile($userId, $tileId, $width, $height, $text, $href, $color, $imageBase64Data, $imageScale) {
        
        $sp = new SPCall("tile_edit");
        $sp->addInParam("user_id", $userId);
        $sp->addInParam("tile_id", $tileId);
        $sp->addInParam("input_width", $width);
        $sp->addInParam("input_height", $height);
        $sp->addInParam("input_text", $text);
        $sp->addInParam("input_href", $href);
        $sp->addInParam("input_color", $color);
        $sp->addInParam("input_imageBase64", $imageBase64Data);
        $sp->addInParam("input_imageScale", $imageScale);
        $sp->addOutParam("result", DBDataType::IntType);

        $spResult = $this->db->executeSP($sp);
        return $spResult->out["result"];
    }

    public function deleteTile($userId, $tileId) {
        $sp = new SPCall("tile_delete");
        $sp->addInParam("user_id", $userId);
        $sp->addInParam("tile_id", $tileId);
        $sp->addOutParam("result", DBDataType::IntType);

        $spResult = $this->db->executeSP($sp);
        return $spResult->out["result"];
    }

    public function removeTileImage($userId, $tileId) {
        $sp = new SPCall("tile_remove_image");
        $sp->addInParam("user_id", $userId);
        $sp->addInParam("tile_id", $tileId);
        $sp->addOutParam("result", DBDataType::IntType);

        $spResult = $this->db->executeSP($sp);
        return $spResult->out["result"];
    }

    public function moveTile($userId, $tileId, $newGridId) {
        $sp = new SPCall("tile_move");
        $sp->addInParam("user_id", $userId);
        $sp->addInParam("tile_id", $tileId);
        $sp->addInParam("input_gridId", $newGridId);
        $sp->addOutParam("result", DBDataType::IntType);

        $spResult = $this->db->executeSP($sp);
        return $spResult->out["result"];
    }

    public function reorderTile($userId, $tileId, $newOrderId) {
        $sp = new SPCall("tile_reorder");
        $sp->addInParam("user_id", $userId);
        $sp->addInParam("tile_id", $tileId);
        $sp->addInParam("input_orderId", $newOrderId);
        $sp->addOutParam("result", DBDataType::IntType);

        $spResult = $this->db->executeSP($sp);
        return $spResult->out["result"];
    }

}

?>