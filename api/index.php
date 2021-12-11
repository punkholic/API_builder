<?php


require 'vendor/autoload.php';

header("Access-Control-Allow-Origin", "*");
header("Access-Control-Allow-Methods", "GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept, x-client-key, x-client-token, x-client-secret, Authorization");

var_dump("hhh");
die();

Flight::route('/', function(){
    require "ui/index.html";
});

Flight::route('/save/@id:[0-9]+', function ( $id ) {
    
    $json = file_get_contents('php://input');
    var_dump( $json );
    die();
    $_POST = json_decode($json, true);
    chdir( "../");
    $root_dir = getcwd();
    
 
    // error_log(print_r($_POST, TRUE));
    if ( !is_dir( $root_dir . '/uploads' )) {
        mkdir( 'uploads' );
    }
    chdir("uploads");
    $uploads_root = getcwd();
   
    // Creating seperate folders for project type
    $language = $_POST['config']['programming-language'];
    $uc_lang = ucfirst( $language );
    
    $path = $uploads_root ."/" . $uc_lang;
    if ( !is_dir( $path )) {
        mkdir( $path );
    }

    if ( !file_exists( $path .'/' . $id . '.json')) {
        fopen( $path . '/' . $id . '.json', "w");
    }
    file_put_contents( $path. '/' . $id . '.json', json_encode($_POST));

    echo json_encode([
        'id' => $id
    ]);
    http_response_code(200);
    exit;
}, 'POST');

Flight::route('/retrieve/@id:[0-9]+', function ( $id ) {
    chdir( "../uploads");
    $search_dir = getcwd();
    $to_match = $id . ".json";
    $json_matches = [];
    $it = new RecursiveDirectoryIterator($search_dir);
    foreach(new RecursiveIteratorIterator($it) as $file) {
        $FILE = array_flip(explode('.', $file));
        if (isset($FILE['json']) ) {
            $dta = explode("/",$file);
            $count = count($dta);
            $json_name = $dta[$count-1];
            if($json_name == $to_match )
            {
                
                $json_matches[] = $dta[$count-2] . "/" . $json_name ;
            }
        }
    }
    $json_total = count( $json_matches );
    $json = $json_matches[ $json_total-1 ];
    $configuration = file_get_contents( $json );
    
    echo $configuration;
    http_response_code(200);
    exit;
}, 'GET');

Flight::start();
