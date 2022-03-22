<?php 
include __DIR__ . "/Classes/Route.php";
include __DIR__."/Classes/Constant.php";
include __DIR__."/Classes/Model.php";
include __DIR__."/Classes/Controller.php";
include __DIR__."/Classes/Migration.php";
include __DIR__."/includes/Common.php";

class Main{
    public function __construct( $jsonFile ){
        $path = "c://Users//Acer//Desktop//APPPP//API_builder//release";
        define("PROJECT_PATH", $path);
        // echo shell_exec( Constant::COMMANDS['CREATE_PROJECT'] . PROJECT_PATH);
        // echo shell_exec( "cd " . PROJECT_PATH . " && composer install");
        $this->jsonInput = json_decode( file_get_contents( $jsonFile ) );
        $this->migration = new Migration( $this->jsonInput );
        $this->route = new Route( $this->jsonInput );
        $this->model = new Model( $this->jsonInput );
        $this->controller = new Controller( $this->jsonInput );
        
    }
}

// $json_dir = __DIR__ . "../../input.json";

// $main = new Main($json_dir);
?>