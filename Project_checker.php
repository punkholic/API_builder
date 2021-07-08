<?php

class Project_checker { 
    protected $env_path = ".env";

    public function __construct() {
        $this->get_project_name();
        $this->find_directory();
        die();
    }
    public function get_project_name() {
        $data = file_get_contents(".env");
        preg_match_all('/(.*)=(.*)/s', $data, $matches);
        for ( $i = 0 ; $i < count( $matches[1] ) ; $i++ ) {
            define( $matches[1][$i], $matches[2][$i] );
        }
        die("ok");
    }
    public function find_directory(){
        if( is_dir() )
        {

        }
        die();
    }
}