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
        $make_auth= MAKE_AUTH;

        
        
        if( ! is_dir( $project_path ) ) {
            echo shell_exec( "composer create-project --prefer-dist laravel/laravel " . $project_path ) ;
            if( isset( $make_auth ) )
            {
                shell_exec( 'sudo chmod -R 777 laravel_default.sh' );
                $command = "cd " . $project_path . "\n";
                $command .= "composer require laravel/ui" . "\n";
                $command .= "php artisan ui:auth" . "\n";
                $command .= "php artisan ui:auth --views" . "\n";
                file_put_contents( 'laravel_default.sh', $command );
                shell_exec("./laravel_default.sh") ;
            }
        }
        shell_exec( 'sudo chmod -R 777 ' . $project_path );

        
        // $this->server_check( $run_server, $project_path );
       
        $this->override_env();

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

    public function override_env()
    {
        $new_env = file_get_contents(".env");
        preg_match_all( '/([\w\S ]+)=([\w\S ]+)/s' , $new_env , $overwrite);

        // preg_match_all( '/([\w\S ]+)=([\w\S ]+)|([\w\S ]+)=/s' , $new_env, $overwrite);

        
        $data = file_get_contents( PROJECT_PATH . "/.env" );
        preg_match_all( '/([\w\S ]+)=([\w\S ]+)/s' , $data, $matches);
        // preg_match_all( '/([\w\S ]+)=([\w\S ]+)|([\w\S ]+)=/s' , $data, $matches);

     

        $new_env_overwrite = ""; 
        $new_env_overwrite .= "\n ";

        for ( $i = 0 ; $i < count( $matches[1] ) ; $i++ ) {
            $matched_checker = FALSE;
            for ( $j = 0 ; $j < count ( $overwrite[1] ) ; $j++ )
            {
                if( $matches[ 1 ][ $i ] == $overwrite[ 1 ][ $j ] )
                {
                    $matches[ 2 ][ $i ] = $overwrite[ 2 ][ $j ];
                    $new_env_overwrite .= trim( $matches[ 1 ][ $i ] ) . '=' . trim( $overwrite[ 2 ][ $j ] ) ;
                    $new_env_overwrite .= "\n ";
                    $matched_checker = TRUE ;
                }
               
            }
            if( ! $matched_checker )
            {
                $new_env_overwrite .= trim( $matches[ 1 ][ $i ] ) . '=' . trim( $matches[ 2 ][ $i ] );
                $new_env_overwrite .= "\n ";
            }
            if( $i % 4 == 0)
            {
                $new_env_overwrite .= "\n ";

            }

        }
        
        file_put_contents( PROJECT_PATH . "/.env", $new_env_overwrite  );
       
        
    }

    public function server_check( $run_server = FALSE, $project_path )
    {
        if( $run_server == "true" && $_SESSION['global_counter'] == 0 )
        {
            $commands = Common::get_basic_script_commands( $project_path );

        }
        else
        {
            $php_version = PHP_VERSION_KILL;
            $_SESSION['global_counter'] = 0;
            shell_exec( "killall -9 php7.4" );
        }
        return ;
    }
    
}
$main = new Main("./input.json");
?>