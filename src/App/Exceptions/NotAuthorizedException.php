<?php

namespace App\Exceptions;

class NotAuthorizedException extends \Exception {

    const CODE = 401;

    public function __construct($message = "not_authorized") {
        parent::__construct($message, self::CODE, null);
    }

}

?>