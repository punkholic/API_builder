<?php
class Controller{

    public function __construct($jsonData){
        $this->jsonInput = $jsonData;
        $this->filePath = PROJECT_PATH . "/app/Http/Controllers/";
        $this->toProcess = ["add", "delete", "view", "edit"];
        $this->processModel($jsonData);
    }
    
    public function processModel(){
        foreach($this->jsonInput as $data){
            $this->processEach($data);
        }
    }

    public function processEach($modelData){
        foreach($this->toProcess as $value){
            // $this->$value($modelData->model->$value, $modelData->model->fields);
        }
    }

    public function view($requestData, $fields){
        foreach($requestData as $value){

            $functionName = $value->request->name;
            $route = $value->request->name;

            if(preg_match("/{(.*)}/i", $route)){
                echo "Has param";
            }
            
            print_r($value);
        }
        // print_r($fields);
        die();
    }

    public function edit($request, $fields){
        print_r($request);
    }

    public function add($request, $fields){
        foreach($requestData as $value){
            $functionName = $value->request->name;
            $route = $value->request->name;

        }
        print_r($request);
    }
    
    public function delete($request, $fields){
        print_r($request);
    }
}
?>