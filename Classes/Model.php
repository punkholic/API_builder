<?php 
class Model{

    public function __construct($input){
        $this->jsonInput = $input;
        $this->filePath = PROJECT_PATH . "/app/Models/";
        $this->processModel();
    }

    public function processModel(){
        foreach($this->jsonInput as $data){
            $this->processEach($data);
        }
    }

    public function processEach($modelData){
        $undefined = [];
        foreach($modelData->model->fields as $key => $value){
            if(!in_array($key, $modelData->model->fillable) && !in_array($key, $modelData->model->guarded)){
                array_push($undefined, $key);
            }
        }
        //creating model
        echo shell_exec("cd ". PROJECT_PATH . " && php artisan make:model " . $modelData->tableName);
        
        $fillable = array_merge($modelData->model->fillable, $undefined);

        // $insertingText = "\n\tprotected \$table = \n\n";
        $insertingText = "\n\tprotected \$fillable = ['" . implode("','", $fillable) . "'];\n\n";
        $insertingText .= "\n\tprotected \$guarded = ['" . implode("','", $modelData->model->guarded) . "'];\n\n}";

        $gotFileData = file_get_contents($this->filePath . $modelData->tableName . ".php");

        //removing pre-define content (if exist)
        $toInsert = preg_replace('/protected \$(fillable|guarded)(.*);/s', '', $gotFileData);
        $toInsert = preg_replace('/\n}/s', $insertingText, $toInsert);

        //writing to file
        file_put_contents($this->filePath . $modelData->tableName . ".php", $toInsert);
    }
}

?>