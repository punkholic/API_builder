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

    public function makeAssoc( $data ){
        $toReturn = [];
        foreach($data as $value){
            $toPut = explode("=", $value);
            $toReturn[$toPut[0]] = $toPut[1];
        }
        return $toReturn;
    }

    public function override_env() {
        $gotData = file_get_contents(".env");
        $toOverride = file_get_contents(PROJECT_PATH . "/.env");
        
        preg_match_all( '/\w+=([\w\S]+){0,}/s' , $gotData, $rawGotData);
        preg_match_all( '/\w+=([\w\S]+){0,}/s' , $gotData, $rawToOverride);
        
        $rawGotData = self::makeAssoc($rawGotData[0]);
        $rawToOverride = self::makeAssoc($rawToOverride[0]);

        $finalData = array_merge($rawGotData, $rawToOverride);
        
        $toWrite = ""; $count = 0;
        
        foreach($finalData as $key => $value){
            $toWrite .= "$key = $value\n";
            $count++;
            if ($count % 4 == 0 ) $toWrite .= "\n\n";
        }
        file_put_contents(PROJECT_PATH . "/.env", $toWrite);
    }

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
}
?>