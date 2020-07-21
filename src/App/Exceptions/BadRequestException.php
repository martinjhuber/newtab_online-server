<?php

namespace App\Exceptions;

class BadRequestException extends \Exception {

    const CODE = 400;
    public $details = null;

    public function __construct($message = "bad_request", $details = null) {
        parent::__construct($message, self::CODE, null);
        $this->details = $details;
    }

}

?>