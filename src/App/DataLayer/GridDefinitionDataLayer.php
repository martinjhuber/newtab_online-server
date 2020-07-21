<?php

namespace App\DataLayer;

use App\Database\SPCall;
use App\Database\Database;

use App\Entities\GridDefinition;
use App\Entities\Tile;

class GridDefinitionDataLayer {

    protected $db;

    public function __construct (Database $db) {
        $this->db = $db;
    }

    public function getGridDefinition($userId) {
        
        $sp = new SPCall("grid_definitions_get");
        $sp->addInParam("user_id", $userId);
        $spResult = $this->db->executeSP($sp);

        $gridDefinition = new GridDefinition();
        foreach ($spResult->rows as $row) {
            $tile = new Tile((int)$row["tileId"], (int)$row["orderId"], (int)$row["width"], (int)$row["height"], $row["href"], $row["text"], $row["color"], $row["imageBase64"], (int)$row["imageScale"]);
            $gridDefinition->addTile((int)$row["gridId"], $tile);
        }
        return $gridDefinition;
    }

}

?>