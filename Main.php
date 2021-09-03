<?php
include __DIR__."/Classes/Model.php";
include __DIR__."/Classes/Controller.php";
include __DIR__."/Classes/Route.php";
include __DIR__."/Classes/Migration.php";
include __DIR__."/includes/Common.php";

class Main{

    public function __construct( $jsonFile ) { 
        $_SESSION['global_counter'] = 0;

        $this->jsonInput = json_decode( file_get_contents( $jsonFile ) );
        $this->jsonInput = Common::validate_timestamp( $this->jsonInput );
        $this->processEnv();

        $this->testing();


        $project_path = PROJECT_PATH ;
        $run_server = RUN_SERVER;
        $make_auth = MAKE_AUTH;

        if( ! is_dir( $project_path ) ) {
            
            echo shell_exec( "composer create-project --prefer-dist laravel/laravel " . $project_path ) ;
            if( $make_auth === "true" )
            {
                shell_exec( 'sudo chmod -R 777 laravel_default.sh' );
                $command = "cd " . $project_path . "\n";
                $command .= "composer require laravel/ui" . "\n";
                $command .= "php artisan ui:auth" . "\n";
                $command .= "php artisan ui:auth --views" . "\n";
                file_put_contents( 'laravel_default.sh', $command );
                shell_exec("./laravel_default.sh") ;
            }
            $file_git_ignore = file_get_contents( ".gitignore" );
            $file_git_ignore .= "\n\n" . $project_path  . "\n";
            $new_file = file_put_contents( ".gitignore" , $file_git_ignore );
            
        }

        echo shell_exec( 'chmod -R 777 ' . $project_path );

        Common::override_env();        
        $this->model = new Model( $this->jsonInput );
        $this->route = new Route( $this->jsonInput );
        $this->controller = new Controller( $this->jsonInput );

        $this->migration = new Migration( $this->jsonInput );

    }
    
    public function processEnv(){
        $data = file_get_contents( ".env" );
        
        preg_match_all( '/(\w+)=(\w+)/s' , $data, $matches);
        
        for ( $i = 0 ; $i < count( $matches[1] ) ; $i++ ) {
            define( trim( $matches[1][$i] ), trim( $matches[2][$i] ) );
        }
    }

    public function testing(){
        // new Controller($this->jsonInput);
        // $this->model->processFile();
    }
    
}
$main = new Main("./input.json");
?>