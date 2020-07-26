<?php

namespace App\Entities;

class GridDefinition {

    public $grids;

    public function __construct($version) {
        $this->version = $version;
        $this->grids = array();
    }

    public function addGrid($id) {
        $this->grids[$id] = new Grid($id);
    }

    public function hasGrid($id) {
        return array_key_exists($id, $this->grids);
    }

    public function addTile($gridId, Tile $tile) {
        if (!$this->hasGrid($gridId)) {
            $this->addGrid($gridId);
        }
        $this->grids[$gridId]->addTile($tile); 
    }

    public function toJson() {
        $grids = array();
        foreach($this->grids as $grid) {
            $grids[] = $grid->toJson();
        }
        return array("version" => $this->version, "grids" => $grids);
    }

}

?>