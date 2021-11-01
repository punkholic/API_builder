<?php
include __DIR__."/Classes/Model.php";
include __DIR__."/Classes/Controller.php";
include __DIR__."/Classes/Route.php";
include __DIR__."/Classes/Migration.php";
include __DIR__."/Classes/Constant.php";
include __DIR__."/includes/Common.php";

class Main{

    public function __construct( $jsonFile ) { 
        $_SESSION['global_counter'] = 0;

        $this->jsonInput = json_decode( file_get_contents( $jsonFile ) );
        $this->jsonInput = Common::validate_timestamp( $this->jsonInput );
        Common::processEnv();

        $this->makeAuth();
    
        Common::override_env();        
        $this->model = new Model( $this->jsonInput );
        $this->route = new Route( $this->jsonInput );
        $this->controller = new Controller( $this->jsonInput );

        $this->migration = new Migration( $this->jsonInput );

    }
    
    

    public function makeAuth(){
        $project_path = PROJECT_PATH ;
        $run_server = RUN_SERVER;
        $make_auth = MAKE_AUTH;

        if( ! is_dir( $project_path ) ) {
            
            echo shell_exec( Constant::COMMANDS['CREATE_PROJECT'] . $project_path ) ;

            if( $make_auth === "true" )
            {
                $command = "cd $project_path && " . Constant::COMMANDS['MAKE_AUTH'];
                echo shell_exec( $command );
            }
            $file_git_ignore = file_get_contents( ".gitignore" );
            if(strpos($file_git_ignore, $project_path) === false){
                $file_git_ignore .= "\n\n" . $project_path  . "\n";
                $new_file = file_put_contents( ".gitignore" , $file_git_ignore );
            }
            
        }
        echo shell_exec( 'chmod 764 ' . $project_path );
    }
}
$main = new Main("./input.json");
?>