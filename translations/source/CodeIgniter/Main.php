<?php 
class Main{
    public function __construct($jsonData){
        $this->jsonInput = json_decode( file_get_contents( $jsonData ) );
            $this->route = new Route( $this->jsonInput );

    }

}
$json_dir = __DIR__ . "/../../../input.json";
$main = new Main($json_dir);
?>