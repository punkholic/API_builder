<?php 
include_once("Skeleton_builder.php");

class Model{

    public function __construct($input){
        $this->jsonInput = $input;
       
        $this->filePath = __DIR__ . PROJECT_PATH . "/app/Models/";
        $this->processModel();
    }

    public function processModel(){
        foreach( $this->jsonInput->data as $data){
            $this->processEach($data);
        }
    }
   
    public function processEach($modelData){
       
        chdir('../release/'); // going to release 
        echo shell_exec( Constant::COMMANDS['MAKE_MODEL'] . " $modelData->tableName");
        $fillable = array_merge($modelData->model->fillable, $undefined);

        $insertingText = "\n\tprotected \$fillable = ['" . implode("','", $fillable) . "'];\n\n";
        $insertingText .= "\n\tprotected \$guarded = ['" . implode("','", $modelData->model->guarded) . "'];\n\n";


        $gotFileData = file_get_contents($this->filePath . $modelData->tableName . ".php");
        $tableName = "\n\tprotected \$table = '$modelData->tableName';\n\n}";
        //removing pre-define content (if exist)
        $toInsert = preg_replace('/protected \$(fillable|guarded)(.*);/s', '', $gotFileData);
        $toInsert = preg_replace('/[ \t]{0,}protected[ \t]{0,}\$table[ \t]{0,}=[ \t]{0,}\'(.*)\'[ \t]{0,};/', $tableName, $gotFileData);
        if(!preg_match('/\$table/', $gotFileData)){
            $toInsert = preg_replace('/\n}/s', $tableName, $toInsert);
        }
        $toInsert = preg_replace('/\n}/s', $insertingText, $toInsert);

        //writing to file
        file_put_contents($this->filePath . $modelData->tableName . ".php", $toInsert);
    }
}

?>