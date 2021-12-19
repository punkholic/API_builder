<?php


require 'vendor/autoload.php';

   

    Flight::route('/', function(){
        require "ui/index.html";
    });
    
    Flight::route('/save/@id:[0-9]+', function ( $id ) {
        
        if (isset( $_SERVER['HTTP_ORIGIN'] )) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
        }
    
        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         
    
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    
            exit(0);
        }
        
        $json = file_get_contents('php://input', TRUE);
       
        $payload_data = json_decode($json, true);
        foreach( $payload_data['config'] as $key => $value ){
         
            if( $key == 'programming_langauge') {
                unset($payload_data['config'][$key]);
                $key = "programming-language";
                $payload_data['config'][$key] = $value;

            }
        }   
       
        chdir( "../");
        $root_dir = getcwd();
        
       
        // error_log(print_r($payload_data, TRUE));
        if ( !is_dir( $root_dir . '/uploads' )) {
            mkdir( 'uploads' );
        }
        chdir("uploads");
        $uploads_root = getcwd();
       
        // Creating seperate folders for project type
        
        $language = $payload_data['config']['programming-language'];
        
        $uc_lang = ucfirst( $language );
       
        $path = $uploads_root ."/" . $uc_lang;
        if ( !is_dir( $path )) {
            mkdir( $path );
        }
    
        if ( !file_exists( $path .'/' . $id . '.json')) {
            fopen( $path . '/' . $id . '.json', "w");
        }
    
        file_put_contents( $path. '/' . $id . '.json', json_encode($payload_data));
    
        echo json_encode([
            'id' => $id
        ]);
        http_response_code(200);
        exit;
    }, 'POST');
    

    Flight::route('/retrieve/@id:[0-9]+', function ( $id ) {
        if (isset( $_SERVER['HTTP_ORIGIN'] )) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
        }
    
        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         
    
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    
            exit(0);
        }
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

    Flight::route('/build_project/@id:[0-9]+', function ( $id ) {
        
        if (isset( $_SERVER['HTTP_ORIGIN'] )) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
        }

        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

            exit(0);
        }
        
        $json = file_get_contents('php://input', TRUE);

        $payload_data = json_decode($json, true);
        $language = $payload_data['programming_language'];

        chdir( "../uploads");
        $root_dir = getcwd();
       
        
        $uc_lang = ucfirst( $language );
       
        $path = $root_dir ."/" . $uc_lang;
        
        $json_data = file_get_contents( $path. '/' . $id . '.json');
       
        chdir("../translations");
        
        include_once(getcwd() . "/Application_builder.php");
        
        $app = new Application_builder($json_data);
    
        $zip_link = $app->build();

        echo json_encode([
            'zip_link' => $zip_link
        ]);
        http_response_code(200);
        exit;
    }, 'POST');
    
    Flight::route('/download_zip', function () {
        
        if (isset( $_SERVER['HTTP_ORIGIN'] )) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
        }

        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

            exit(0);
        }
        
        $json = file_get_contents('php://input', TRUE);

        $payload_data = json_decode($json, true);
        $zip_path = $payload_data['zip_path'];

        
        if (file_exists($zip_path)) {
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . basename($zip_path) . '"');
            header('Content-Length: ' . filesize($zip_path));
            flush();
         }
        
       
        echo json_encode([
            'success' => "Success"
        ]);
        http_response_code(200);
        exit;
    }, 'POST');

Flight::start();


