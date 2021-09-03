<?php
class Controller{

    public function __construct($jsonData){
        $this->jsonInput = $jsonData;
        $this->filePath = PROJECT_PATH . "/app/Http/Controllers/";
        $this->toProcess = ["add", "delete", "view", "edit"];
        $this->processModel();
    }
    
    public function processModel(){
        foreach($this->jsonInput as $data){
            $this->processEach($data);
        }
    }

    public function processEach($modelData){
        foreach($this->toProcess as $value){
            $this->$value($modelData->model->$value, $modelData->model->fields);
        }
    }

    public function getFunctionParams($data){
        $functionName = $data->request->name;
        $route = $data->request->route;
        $toReturn = "public function $functionName(Request \$request";

        preg_match_all('/{[\w\d$_]+}/i', $route, $matched);
        if(!empty($matched[0])){
            foreach($matched[0] as $value){
                $toReturn .= ", " . preg_replace('/[{}]/', "", $value);
            }
        }
        $toReturn .= ")";
        return $toReturn;        
    }

    public function view($requestData, $fields){
        // echo "asd";
        foreach($requestData as $value){

            $functionName = $value->request->name;
            $route = $value->request->name;
            
            if(preg_match("/{(.*)}/i", $route)){
                echo "Has param";
            }
            
        }
    }

    public function edit($requestData, $fields){
    }

    public function add($requestData, $fields){
        foreach($requestData as $value){
            $functionTop = $this->getFunctionParams($value);
            $this->getFieldTypes($value->fields, $fields);
        }
        die();
    }
    
    public function delete($requestData, $fields){

    }

    public function getFieldTypes( $fields, $allFields ){
        foreach($fields as $value){
            echo $value;
            // if(array_key_exists($value, $allFields)){
            //     echo $allFields[$value];
            // }
        }
    }
}
?>