<?php
include __DIR__."/Classes/Model.php";
include __DIR__."/Classes/Controller.php";
include __DIR__."/Classes/Route.php";
include __DIR__."/Classes/Migration.php";
include __DIR__."/Classes/Constant.php";
include __DIR__."/includes/Common.php";

class Main{

    public function __construct( $jsonFile ) { 
        $path = "c://Users//Acer//Desktop//APPPP//API_builder//release";
        // echo shell_exec("rm -rf $path");
        // echo shell_exec("mkdir $path");
        define("PROJECT_PATH", $path);
        // echo shell_exec( Constant::COMMANDS['CREATE_PROJECT'] . PROJECT_PATH);
        // echo shell_exec( "cd " . PROJECT_PATH . " && composer install");

        $_SESSION['global_counter'] = 0;

        $this->jsonInput = json_decode( ( $jsonFile ) );
        $this->jsonInput = Common::validate_timestamp( $this->jsonInput );
     
        // Common::processEnv( $this->jsonInput );

        // $this->makeAuth();
    
        // Common::override_env();  
      
        $this->model = new Model( $this->jsonInput );
        $this->route = new Route( $this->jsonInput );
        $this->controller = new Controller( $this->jsonInput );

        $this->migration = new Migration( $this->jsonInput );
        // Common::clear_initial_env();
        
    }
    
}
// $json_dir = trim(shell_exec("cd ../../../ && pwd")) . "/input.json";

// $main = new Main($json_dir);
?>