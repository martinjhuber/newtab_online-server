<?php

namespace App\Exceptions;

class NotFoundException extends \Exception {

    const CODE = 404;

    public function __construct($message = "entity_not_found") {
        parent::__construct($message, self::CODE, null);
    }

}

?>