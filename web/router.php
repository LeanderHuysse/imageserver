<?php

// Bootstrap the autoload file
require_once("vendor/autoload.php");

use IOLabs\Controller\RequestController as RequestController;
$requestController = new RequestController();

// Process the incoming request
if($requestController->validateRequest($_GET)) {
    // Check if the requested image exists
    if($requestController->srcExists()) {

        $setWidth = 500;
        $setHeight = 0;

        if(isset($_GET['w'])) {
            $setWidth = $_GET['w'];
        }

        if(isset($_GET['h'])) {
            $setHeight = $_GET['h'];
        }

        $imageData = [
            'w' => $setWidth,
            'h' => $setHeight
        ];

        /**
         * What kind of request is this?
         *
         * - Single image
         * - Gallery
         * - Featured image
         */

        $requestController->serveImage($imageData);

    }
}

