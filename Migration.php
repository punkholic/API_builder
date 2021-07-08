<?php 
class Migration{

    public function __construct($jsonData){
        $this->jsonInput = $jsonData;
        $this->processModel();
    }

    public function processModel(){
        foreach($this->jsonInput as $data){
            $this->processEach($data);
        }
    }
    public function processEach($modelData){
        print_r($modelData->model->fields);
    }
}

?>