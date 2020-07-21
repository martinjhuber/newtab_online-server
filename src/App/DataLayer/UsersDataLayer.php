<?php

namespace App\DataLayer;

use App\Database\SPCall;
use App\Database\Database;
use App\Database\DBDataType;

use App\Entities\User;
use App\Entities\Token;

class UsersDataLayer {

    protected $db;

    public function __construct (Database $db) {
        $this->db = $db;
    }

    public function login($name, $password) {

        $sp = new SPCall("user_login");
        $sp->addInParam("input_name", $name);
        $sp->addInParam("input_password", $password);
        $sp->addOutParam("user_id", DBDataType::IntType);
        $sp->addOutParam("token", DBDataType::StringType);
        $sp->addOutParam("result", DBDataType::IntType);
        $r = $this->db->executeSP($sp);

        if ($r->out["result"] === 1) {
            return array(new User($r->out["user_id"], $name), new Token($r->out["token"]));
        }

        return null;
    }

    public function logout($token) {

        $sp = new SPCall("user_logout");
        $sp->addInParam("user_token", $token);
        $this->db->executeSP($sp);
        return true;

    }

    public function getUserDetails($id) {

        $sp = new SPCall("user_get");
        $sp->addInParam("user_id", $id);
        $r = $this->db->executeSP($sp);

        if (count($r->rows) === 1) {
            $row = $r->rows[0];
            return new User($row["id"], $row["name"]);
        }

        return null;
    }

    public function getUserByToken($token, $extendExpiry = true) {

        $sp = new SPCall("user_token_verify");
        $sp->addInParam("check_token", $token);
        $sp->addInParam("extendExpiry", $extendExpiry);
        $sp->addOutParam("user_id", DBDataType::IntType);
        $sp->addOutParam("result", DBDataType::IntType);
        $r = $this->db->executeSP($sp);

        if ($r->out["result"] === 1) {
            return $this->getUserDetails($r->out["user_id"]);
        }

        return null;
    }

}

?>