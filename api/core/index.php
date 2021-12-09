<?php
require 'flight/Flight.php';

Flight::route('/', function(){
    include_once __DIR__ . "../ui/index.html" ;
});

Flight::add('/save/([0-9]*)', function ($id) {
    $json = file_get_contents('php://input');
    $_POST = json_decode($json, true);

    error_log(print_r($_POST, TRUE));
    if (!is_dir('../uploads')) {
        mkdir('uploads');
    }
    // For testing the generated configuration and test the working
    // file_put_contents('../sample/videoportfolio/test.json', json_encode($_POST));
    if (!file_exists('../uploads/' . $id . '.json')) {
        fopen('../uploads/' . $id . '.json', "w");
    }
    file_put_contents('../uploads/' . $id . '.json', json_encode($_POST));

    echo json_encode([
        'id' => $id
    ]);
    http_response_code(200);
    exit;
}, 'POST');

Flight::start();
