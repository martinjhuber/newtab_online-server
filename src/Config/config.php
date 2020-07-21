<?php

// Result Tracker Service configuration
return [
    "app.debugMode" => false,
    "database" => [
        "type" => "mysql",
        "host" => "localhost",
        "database" => "newtab",
        "user" => "newtab",
        "password" => "newtab#pw51"
    ],
    "api.basePath" => "/api/v1/",
    "token.header" => "X-NTS-Token"
];

?>