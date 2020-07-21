<?php

namespace App\Database;

use \PDO;

class DBDataType {
    const StringType = 0;
    const BoolType = 1;
    const IntType = 2;
    const FloatType = 3;
}

class SPCallResult {
    public $out = array();
    public $rows = array();
    public $rowSets = array();
}

class SPCall {

    protected $spName = null;
    protected $params = array();
    protected $inParams = array();
    protected $outParams = array();

    public function __construct($spName) {
        $this->spName = $spName;
    }

    public function addInParam($name, $value) {
        $this->inParams[] = ["mode" => "in", "name" => $name, "value" => $value];
        $this->params[] = $this->inParams[count($this->inParams)-1];
    }

    public function addOutParam($name, $type = DBDataType::StringType) {
        $this->outParams[] = ["mode" => "out", "name" => $name, "type" => $type];
        $this->params[] = $this->outParams[count($this->outParams)-1];
    }

    // Builds for two in and two out parameters:
    // CALL <spName>(?,?,@outParam1,@outParam2); SELECT @outParam1 as outParam1, @outParam2 as outParam2;
    private function getPrepareStatement() {
        $spParams = "";
        $selectParams = "";

        for ($i = 0; $i < count($this->params); ++$i) {
            $param = $this->params[$i];
            if ($i !== 0)
                $spParams .= ",";

            if ($param["mode"] == "in") {
                $spParams .= "?";
            } else {
                $spParams .= "@".$param["name"];
                if (strlen($selectParams) > 0)
                    $selectParams .= ", ";
                $selectParams .= "@".$param["name"] . " as " . $param["name"];
            }
        }

        $prepare = "CALL ".$this->spName."(".$spParams.");";
        if (strlen($selectParams) > 0)
            $prepare .= " SELECT ".$selectParams.";";

        return $prepare;
    }

    public function execute(PDO $dbh) {

        // Prepare statement
        $statement = $dbh->prepare($this->getPrepareStatement());

        // Bind in parameters
        foreach ($this->inParams as $i => $param) {
            if ($param["mode"] === "in") {
                $statement->bindParam($i+1, $param["value"]);
            }
        }

        $statement->execute();

        // Get all row sets.
        // If there are no output parameters, all sets are from within the SP
        // If there are N sets and output parameters, sets 0 to N-1 are row sets from within the SP, set N contains the output values
        $rowSets = array();
        do {
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
            if ($rows) {
                $rowSets[] = $rows;
            }
        } while ($statement->nextRowset());
        $statement = null;

        $result = new SPCallResult();

        // Extract output parameter values
        if (count($this->outParams) > 0) {
            $outSet = array_pop($rowSets);    // last result set
            $outValues = $outSet[0];          // first and only row

            // Cast values to proper types
            foreach ($this->outParams as $i => $param) {
                $name = $param["name"];
                $value = $outValues[$name];
                if ($param["type"] == DBDataType::IntType) {
                    $result->out[$name] = (int)$value;
                } elseif ($param["type"] == DBDataType::FloatType) {
                    $result->out[$name] = (float)$value;
                } elseif ($param["type"] == DBDataType::BoolType) {
                    $result->out[$name] = (bool)$value;
                } else {
                    $result->out[$name] = $value;
                }
            }

        }

        if (count($rowSets) > 0) {
            $result->rows = $rowSets[0];
            $result->rowSets = $rowSets;
        }

        return $result;

    }

}

?>