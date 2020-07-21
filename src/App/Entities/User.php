<?php

namespace App\Entities;

class User {

    public $id;
    public $name;

    public function __construct($id, $name) {
        $this->id = $id;
        $this->name = $name;
    }

    public function toJson() {
        return array("user" => array("id" => $this->id, "name" => $this->name));
    }

}

?>