<?php

namespace App\Entities;

class Token {

    public $token;

    public function __construct($token) {
        $this->token = $token;
    }

    public function toJson() {
        return array("token" => $this->token);
    }

}

?>