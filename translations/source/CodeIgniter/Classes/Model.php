<?php 

class Model{

    public function __construct($input){
        $this->jsonInput = $input;
       
        $this->filePath = PROJECT_PATH . "/app/Models/";
        $this->processModel();
    }

    public function processModel(){
        foreach( $this->jsonInput->data as $data){
            $this->processEach($data);
        }
    }

    public function checkName($name){
        $dirs = scandir(PROJECT_PATH . "/app/Models");
        foreach($dirs as $dir){
            if (!in_array($dir, array(".",".."))){
                $fileNameToCheck = $dir;
                if (strpos($dir, ".") !== FALSE){
                    $dir = explode(".", $dir)[0];
                }

                if(strcasecmp($name, $dir) === 0){
                    return $fileNameToCheck;
                }
            }
        }
        return null;
    }

    public function getArrayString($arrayName, $array, $type){
        $toReturn = "\$$arrayName = [";
        // if(array_values($array) !== $array){ didnt worked
        if($type !== "list"){
            // dict
            foreach($array as $key => $value){
                $toReturn .= "'$key' => '$value', ";
            }
        }else{
            foreach($array as $value){
                $toReturn .= "'$value', ";
            }
        }
        $toReturn .= "];";
        return $toReturn;
    }

    public function processEach($modelData){
        chdir(PROJECT_PATH); // going to release 
        echo shell_exec( Constant::COMMANDS['MAKE_MODEL'] . " $modelData->tableName");

        $fileName = $this->checkName($modelData->tableName);
        $gotFileData = file_get_contents("$this->filePath/$fileName");

        $primary = "id";
        foreach($modelData->model->fields as $key => $value){
            if(strcmp($value, "primary") == FALSE){
                $primary = $key;
            }
        }

        $undefined = [];
        foreach( $modelData->model->fields as $key => $value){
           
            if( ! in_array( $key, $modelData->model->fillable ) && ! in_array( $key, $modelData->model->guarded ) ){
                array_push( $undefined, $key );
            }
        }

        $fillable = array_merge($modelData->model->fillable, $undefined);

        $toSet = [
                    "table" => $modelData->tableName,
                    "primaryKey" => $primary,
                    "useSoftDeletes" => "true",
                    "useTimestamps" => "false",
                    "allowedFields" => $fillable,
        ];

        foreach($toSet as $key => $value){
            $toReplace = "protected ";
            if(!is_array($value)){
                if($value == "true" || $value == "false"){
                    $toReplace .= "$$key = $value;";
                }else{
                    $toReplace .= "$$key = '$value';";
                }
                
            }else{
                $toReplace .= $this->getArrayString($key, $value, "list");
            }
            $gotFileData = preg_replace("/protected(.*)$key(.*);/", $toReplace, $gotFileData);
        }
        file_put_contents($this->filePath . $fileName, $gotFileData);
    }
}

?>