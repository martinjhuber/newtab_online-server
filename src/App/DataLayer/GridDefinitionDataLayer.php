<?php

namespace App\DataLayer;

use App\Database\SPCall;
use App\Database\Database;
use App\Database\DBDataType;

use App\Entities\GridDefinitionVersion;
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
        $sp->addOutParam("version", DBDataType::IntType);
        $spResult = $this->db->executeSP($sp);

        $gridDefinition = new GridDefinition($this->checkVersion($spResult->out["version"]));
        foreach ($spResult->rows as $row) {
            $tile = new Tile((int)$row["tileId"], (int)$row["orderId"], (int)$row["width"], (int)$row["height"], $row["href"], $row["text"], $row["color"], $row["imageBase64"], $row["imageScale"]);
            $gridDefinition->addTile((int)$row["gridId"], $tile);
        }
        return $gridDefinition;
    }

    public function getGridDefinitionVersion($userId) {
        
        $sp = new SPCall("grid_definition_version_get");
        $sp->addInParam("user_id", $userId);
        $sp->addOutParam("version", DBDataType::IntType);
        $spResult = $this->db->executeSP($sp);

        return new GridDefinitionVersion($this->checkVersion($spResult->out["version"]));
    }    

    private function checkVersion($value) {
        $version = -1;
        if (isset($value)) {
            $version = (int)$value;
        }
        return $version;
    }

}

?>