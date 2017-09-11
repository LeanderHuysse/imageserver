<?php
require("src/Request.php");

$request = new Request($_GET);

if($request->check()) {
    $request->serve();
}