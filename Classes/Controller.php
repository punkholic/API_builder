<?php
class Controller{

    public function __construct($jsonData){
        $this->jsonInput = $jsonData;
        $this->filePath = PROJECT_PATH . "/app/Http/Controllers/";
        $this->toProcess = ["add", "edit", "delete", "view" ];
        $this->processModel();
    }
    
    public function processModel(){
        foreach($this->jsonInput as $data){
            $this->processEach($data);
        }
    }

    public function processEach($modelData){
        foreach($this->toProcess as $value){
            $this->$value($modelData->model->$value, $modelData->model->fields, $modelData);
        }
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
    
    public function getFunctionParams($data){
        $functionName = $data->request->name;
        $route = $data->request->route;
        $toReturn = "public function $functionName(Request \$request";

        preg_match_all('/{[\w\d$_]+}/i', $route, $matched);
        if(!empty($matched[0])){
            foreach($matched[0] as $value){
                $toReturn .= ", $" . preg_replace('/[{}]/', "", $value);
            }
        }
        $toReturn .= ")";
        return $toReturn;        
    }

    public function view($requestData, $fields, $modelData){
        // echo "asd";
        foreach($requestData as $value){

            $functionName = $value->request->name;
            $route = $value->request->name;
            
            if(preg_match("/{(.*)}/i", $route)){
                echo "Has param";
            }
            
        }
    }

    public function edit($requestData, $fields, $modelData){

    }

    public function add($requestData, $fields, $modelData){
        $fieldsInfo = $this->getFieldTypes($fields);

        foreach($requestData as $value){
            $functionTop = $this->getFunctionParams($value);

            $optionalFields = array_intersect($value->fields, $fieldsInfo['optional']);
            $requiredFields = array_intersect($value->fields, $fieldsInfo['required']);

            $optionalString = $this->getArrayString("optionalField", $optionalFields, "list");
            $requiredString = $this->getArrayString("mustHave", $requiredFields, "list");
            
            $optionalHash = array_intersect($fieldsInfo['hash'], $fieldsInfo['optional']);
            $requiredHash = array_intersect($fieldsInfo['hash'], $fieldsInfo['required']);
            
            $optionalHashString = ""; $requiredHashString = "";
            
            foreach($optionalHash as $value){
                $optionalHashString .= <<<text
                    if(array_key_exists('$value', \$toStore)){
                            \$toStore['$value'] = Hash::make(\$toStore['$value']);
                        }
                    text;
            }

            foreach($requiredHash as $value){
                $requiredHashString .= "\$toStore['$value'] = Hash::make(\$toStore['$value']);\n";
            }

            $toReturn = <<<text
                $functionTop { 
                    $optionalString
                    $requiredString
                    \$toValidate = [];
                    for(\$i = 0; \$i < count(\$mustHave); \$i++){
                        \$toValidate[\$mustHave[\$i]] = ['required'];
                        \$toStore[\$mustHave[\$i]] = \$request->get(\$mustHave[\$i]);
                    }
                    \$toStore = [];
                    for(\$i = 0; \$i < count(\$mustHave); \$i++){
                        \$toStore[\$mustHave[\$i]] = \$request->get(\$mustHave[\$i]);
                    }
                    $requiredHashString
                    $optionalHashString
                    $modelData->tableName::insert(\$toStore);
                    return ["Success" => true]; 
                }
                text;
                // echo $toReturn . "\n\n\n\n\n\n\n\n";
        }
    }
    
    public function delete($requestData, $fields, $modelData){

    }

    public function getFieldTypes($data){
        $toReturn = ["required" => [], "optional" => []];
        foreach($data as $key => $value){
            if (is_numeric(strpos($value, "required"))){
                $toReturn["required"][] = $key;
            }else{
                $toReturn["optional"][] = $key;
            }

            if (is_numeric(strpos($value, "hash"))){
                $toReturn["hash"][] = $key;
            }
            
        }
        return $toReturn;
    }
}
?>