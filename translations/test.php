<?php
include_once("Application_builder.php");

$app = new Application_builder(file_get_contents(__DIR__ . '/../input.json'));
$app->build();

$path = "../release";
shell_exec("sudo chmod -R 777 " . $path );