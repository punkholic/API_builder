<?php 
include __DIR__ . "/Classes/Route.php";

class Main{
    public function __construct( $jsonFile ){
        define("PROJECT_PATH", "/../../../../release");
        $this->jsonInput = json_decode( file_get_contents( $jsonFile ) );
        $this->route = new Route( $this->jsonInput );
        // $this->model = new Model( $this->jsonInput );
    }
}

$json_dir = __DIR__ . "/../../../input.json";

$main = new Main($json_dir);
?>