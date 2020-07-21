<?php

namespace App\Helpers;

use App\Exceptions\BadRequestException;

class RequestVerifier {

    public function __construct () { }

    public function isContainer($v) {
        if (!isset($v) || !is_array($v)) {
            throw new BadRequestException("body_structure_error", "Expected object does not exist.");
        }
    }

    public function verifyParamExist($obj, $param) {
        if (!array_key_exists($param, $obj)) {
            throw new BadRequestException("missing_parameter", "Missing parameter '$param'.");
        }
        return true;
    }

    public function verifyString($obj, $param, $minLength = null, $maxLength = null) {
        $this->verifyParamExist($obj, $param);
        $v = $obj[$param];
        if (!isset($v) || ($minLength !== null && strlen($v) < $minLength) || ($maxLength !== null && strlen($v) > $maxLength)) {
            throw new BadRequestException("invalid_string", "Invalid string parameter '$param'.");
        }
        return $v;
    }

    public function verifyInteger($obj, $param, $minValue = null, $maxValue = null) {
        $this->verifyParamExist($obj, $param);
        $v = $obj[$param];
        if (!isset($v) || ($minValue !== null && (int)$v < $minValue) || ($maxValue !== null && (int)$v > $maxValue)) {
            throw new BadRequestException("invalid_integer", "Invalid integer parameter '$param'.");
        }
        return (int)$v;
    }

}

?>