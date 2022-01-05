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
            $remove_whitespace = explode( " ", $value );
            is_array( $remove_whitespace ) ? ( $new_value = implode("_",$remove_whitespace) ) : $new_value = $value;
                   
            switch ( $key ) {
                case 'programming_langauge' : 
                    $key = "programming-language";
                    $payload_data['config'][$key] = $value;
                break;
                case 'app_name' : 
                case 'database_name' : 
                case 'database_username' : 
                    $payload_data['config'][$key] = $new_value;
                break;
            }
        }  
        $whitelisted_data_payload = [];
        foreach( $payload_data['data'] as $data_key => $data_value ){
            
            $name_wspace = explode( " ", $data_value['tableName'] );
            is_array( $name_wspace ) ? ( $data_value['tableName'] = implode( "_", $name_wspace) ) : $data_value['tableName'] = $data_value['tableName'];
            
            $controller_name_wspace = explode( " ", $data_value['controller'] );
            is_array( $controller_name_wspace ) ? ( $data_value['controller'] = implode( "_", $controller_name_wspace) ) : $data_value['controller'] = $data_value['controller'];
            
            $model = $data_value['model'];
            if( $model ) {
                foreach( $model as $model_key => $model_value ) {
                    if( is_array( $model_value ) ) {
                        foreach( $model_value as $m_key => $m_val ) {
                           
                            if( ! is_array( $m_val ) ) {
                                $value_w_space = explode( " ", $m_val );
                                is_array( $value_w_space ) ? ( $m_val = implode( "_", $value_w_space) ) : $m_val = $m_val ;
                                if( ! is_numeric( $m_key ) ) {
                                    $remove_key_w_space = explode( " ", $m_key );
                                    if( is_array( $remove_key_w_space ) ) { 
                                        $model_value[implode( "_", $remove_key_w_space)] = $m_val ;
                                        unset($model_value[$m_key]);
                                    }
                                } else if ( is_numeric( $m_key ) ) {
                                    $model_value[$m_key] = $m_val;
                                }  
                            } else if ( is_array( $m_val ) ) {
                                foreach( $m_val as $k => $v ) {
                                    if( is_array( $v ) ) {
                                        foreach ( $v as $f_k => $f_v ) {
                                            $final_replacement = explode( " ", $f_v );
                                            is_array( $final_replacement ) ? ( $v[$f_k] = implode( "_", $final_replacement) ) : $v[$f_k] = $f_v ;
                                
                                        }
                                    }
                                 
                                    $m_val[$k] = $v;
                                }
                                $model_value[$m_key] =  $m_val;

                            }

                        }

                    }
                    $model[$model_key] = $model_value;
                }
                $data_value['model'] = $model;
            }  
            $whitelisted_data_payload[] = $data_value;
        } 
        $payload_data['data'] = $whitelisted_data_payload;  
      
    
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
        $language = $payload_data['config']['programming-language'];
 
      
        chdir( "../uploads");
        $root_dir = getcwd();
       
        
        $uc_lang = ucfirst( $language );
       
        $path = $root_dir ."/" . $uc_lang;
        
        $json_data = file_get_contents( $path. '/' . $id . '.json');
       
        chdir("../translations");
        
        include_once(getcwd() . "/Application_builder.php");
       
        $app = new Application_builder( $json_data );
    
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
     
        $zip_file = $payload_data['zip_path'];
        $zip_data = explode("/", $zip_file );
        $uri_count = count( $zip_data );
        $file_name = $zip_data[$uri_count-1];
        $file_folder_name = $zip_data[$uri_count-2];

        chdir("../PastProjects/".$file_folder_name );
        $projects_dir = getcwd();
    
        header('Content-Type: application/zip');
        header("Content-Type: application/force-download");
        header('Content-Disposition: attachment; filename="xx.zip"');
        // header('Content-Length: ' . filesize($zip_file));
        flush();
        var_dump(readfile($zip_file));
        die();
        // header('Content-disposition: attachment; filename=Api_builder_project.zip');
        // header('Content-type: application/zip');
        // readfile($zip_path);

        // header("Content-type: application/zip");
        // header("Content-Disposition: attachment; filename=xyz.zip");
        // header("Pragma: no-cache");
        // header("Expires: 0");
        // readfile($zip_file);
        exit;
       
        echo json_encode([
            'success' => "Success"
        ]);
        http_response_code(200);
        exit;
    }, 'POST');

Flight::start();


