<?php 
class Common{

    private function __constructor(){
        
    }

    private static function getDefault( $args, $default, $passedArgs ){
        //initilize default data
        foreach($args as $value){
            if(!array_key_exists($value, $passedArgs)){
                if(!array_key_exists($value, $default)){
                    echo $value;
                    //if must have arguments is not present (must have = not in default but in args)
                    return false;
                }
                $passedArgs[$value] = $default[$value];
            }
        }
        return $passedArgs;
    }

    public static function renderFunction( $args ){
        $arguments = ['functionType', 'functionName', 'lines', 'tabs', 'text'];
        $defaultValues = [
            "functionType" => "",
            "functionName" => "",
            "tabs" => 1,
            "lines" => 1,
            "text" => "",
            "params" => ""
        ];
        $args = self::getDefault($arguments, $defaultValues, $args);
        if(!$args){
            echo "Invalid data passed";
            return;
        }

        $toReturn = "function " . $args['functionType'] . " " .$args['functionName']." ( ". $args['params'] ." ) {\n";
        $newLine = str_repeat("\n", $args['lines']);
        $tabs = str_repeat("\t", $args['tabs']);
        $toRender = explode("\n", $args['text']);

        foreach( $toRender as $data ){
            $toReturn .= "$tabs$data$newLine";
        }
        $toReturn .= "\t}";
        return $toReturn;
    }
    /**
     * Function to validate and add if timestamp key is set to true in the input.json file.
     */
    public static function validate_timestamp( $json )
    {
        if( is_array( $json ) )
        {
            foreach( $json as &$value )
            {
            
                   
                    switch ( $value->model->timestamps ) {
                        case TRUE:                         
                            $model_prop = $value->model;
                          
                            if( $model_prop->timestamps )
                            {
                                $value->model->fields->created_at = "date";
                                $value->model->fields->updated_at = "datetime";

                                $value->model->fillable[] = "created_at";
                                $value->model->fillable[] = "updated_at";
                            }
                            break;
                        default:
                            break;
                    }
               
                    
            }   
        }
    
        return $json;
    } 
    /**
     * Function to Generate / write the dynamic script in server.sh file.
     */
    public function get_basic_script_commands( $project_path )
    {
        if( isset( $project_path ) )
        {
            $server_port = SERVER_PORT;
            $common_command = "cd " . $project_path . "\n";
            
            if( $_SESSION['global_counter'] == 0 )
            {
                if( isset( $server_port ) )
                {
                    $common_command .= "nohup php artisan serve --port=$server_port & > /dev/null 2>&1"  ;
        
                }
                else
                {
                    $common_command .= "nohup php artisan serve & > /dev/null 2>&1";
                }

                
                $_SESSION['global_counter'] += 1 ; 
            }
            

            
            file_put_contents( "server.sh", $common_command );
            shell_exec( "chmod -R 777 server.sh" );
            shell_exec( "./server.sh" );

        }
              
    }
}
?>