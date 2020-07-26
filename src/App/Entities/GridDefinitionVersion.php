<?php

namespace App\Entities;

class GridDefinitionVersion {

    protected $grids;

    public function __construct($version) {
        $this->version = $version;
    }

    public function toJson() {
        return array("version" => $this->version);
    }

}

?>