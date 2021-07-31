<?php
include __DIR__."/Classes/Model.php";
include __DIR__."/Classes/Controller.php";
include __DIR__."/Classes/Route.php";
include __DIR__."/Classes/Migration.php";
include __DIR__."/includes/Common.php";

class Main{

    public function __construct( $jsonFile ) { 
        
        $this->jsonInput = json_decode( file_get_contents( $jsonFile ) );
        
        $this->processEnv();

        $this->testing();


        $project_path = PROJECT_PATH ;
   
        if( ! is_dir( $project_path ) ) {
            echo shell_exec( "composer create-project --prefer-dist laravel/laravel " . $project_path ) ;
        }

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
        preg_match_all( '/([\w\S ]+)=([\w\S ]+)/s' , $new_env, $overwrite);

        
        $data = file_get_contents( PROJECT_PATH . "/.env" );
        preg_match_all( '/([\w\S ]+)=([\w\S ]+)/s' , $data, $matches);


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
        // $env_file_path = fopen( PROJECT_PATH . "/.env" , "w" );
        // if( !$env_file_path )
        // {
        //     return FALSE;
        // }
        
    }
    
}
$main = new Main("./input.json");
?>