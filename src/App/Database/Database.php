<?php

namespace App\Database;

use \PDO;
use Psr\Container\ContainerInterface;
use App\Database\SPCall;

class Database {

    protected $config;
    protected $connected = false;
    protected $dbhandle = null;

    public function __construct(ContainerInterface $container) {
        $this->config = $container->get("config")["database"];

        $this->initDb();
    }

    protected function initDb() {
        try {
            $this->dbhandle = new PDO($this->config["type"].":host=".$this->config["host"].";dbname=".$this->config["database"], $this->config["user"], $this->config["password"]);
            $this->connected = true;
        } catch (\Exception $ex) {}
    }

    public function getHandle() {
        return $this->dbhandle;
    }

    public function executeSP(SPCall $spCall) {
        if (!$this->connected) {
            throw new \Exception("no_db_connection");
        }
        return $spCall->execute($this->dbhandle);
    }

    public function close() {
        $this->dbhandle = null;
    }

}

?>