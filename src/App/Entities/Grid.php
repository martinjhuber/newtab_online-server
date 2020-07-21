<?php

namespace App\Entities;

class Grid {

    public $gridId;
    public $tiles;

    public function __construct($id) {
        $this->gridId = $id;
        $this->tiles = array();
    }

    public function addTile(Tile $tile) {
        $this->tiles[] = $tile;
    }

    public function toJson() {
        $tiles = array();
        foreach($this->tiles as $tile) {
            $tiles[] = $tile->toJson();
        }
        return array("grid" => $this->gridId, "tiles" => $tiles);
    }
}

?>