<?php
class Controller{

    public function __construct($jsonData){
        $this->jsonInput = $jsonData;
        $this->filePath = PROJECT_PATH . "/app/Http/Controllers/";
        $this->toProcess = ["view", "edit", "add", "delete" ];
        $this->controllerHeading = [
            "use Illuminate\\\Http\\\Request;",
            "use Illuminate\\\Support\\\Facades\\\Hash;"
        ];
        $this->processModel();
    }
    
    public function processModel(){
        foreach($this->jsonInput as $data){
            echo shell_exec("cd " . PROJECT_PATH . " && " . Constant::COMMANDS['MAKE_CONTROLLER'] . " " . $data->controller );
            $this->processEach($data);
        }
    }

    public function processEach($modelData){
        $toPut = "";
        foreach($this->toProcess as $value){
            $toPut .= $this->$value($modelData->model->$value, $modelData->model->fields, $modelData) . "\n\n";
        }
        echo $modelData->controller;

        $controllerCode = file_get_contents("$this->filePath$modelData->controller.php");
       
        $toCheck = $this->controllerHeading;
        $toCheck[] = "use App\Models\\{$modelData->tableName}";
        
        foreach($toCheck as $value){
            if(!preg_match("/$value/i", $controllerCode)){
                $toInsert = "$value\n class";
                $controllerCode = preg_replace("/\nclass/i", $toInsert, $controllerCode);
            }
        }
        $toPut = "extends Controller\n{\n\t$toPut";

        $toPut = preg_replace("/extends[ ]+Controller[\n\t ]+{/i", $toPut, $controllerCode);
        file_put_contents("$this->filePath$modelData->controller.php", $toPut);
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
        $allFields = array_keys((array)$fields);
        
        foreach($requestData as $value){
            $functionTop = $this->getFunctionParams($value);

            $whereClause = $this->getRouteParam($value->request->route)["whereClause"];
            $selectData = "*";
            $validFields = (array_intersect($allFields, $value->fields));
            if(!empty($validFields)){
                $selectData = "";
                foreach($validFields as $value){
                    $selectData .= "'$value', ";
                }
                $selectData = substr($selectData, 0, strlen($selectData) - 2);
            }

            $toReturn = <<<text
            $functionTop{
                \$data =  $modelData->tableName::{$whereClause}select($selectData)->get();
                return \$data;
            }
            text;
            return $toReturn;
        }
    }
    public function getRouteParam($route){
        preg_match_all('/{[\w\d]+}/', $route, $matched);

        $fields = [];
        $whereClause = "";
        
        foreach($matched[0] as $params){
            $filteredField = preg_replace('/[{}]/', "", $params);
            $fields[] =  $filteredField;
            $whereClause .= "where('$filteredField', $$filteredField)->";
        }
        return ["whereClause" =>$whereClause, "fields" => $fields];
    }

    public function edit($requestData, $fields, $modelData){
        $fieldsInfo = $this->getFieldTypes($fields);
        foreach($requestData as $value){

            $functionTop = $this->getFunctionParams($value);
            $updateFieldString = $this->getArrayString("updateFields", $value->fields, "list");
            $whereClause = $this->getRouteParam($value->request->route)["whereClause"];
            
            
            $toReturn = <<<text
            $functionTop
            {
                $updateFieldString
                \$toUpdate = [];
                foreach(\$updateFields as \$value){
                    if(\$request->get(\$value) != null){
                        \$toUpdate[\$value] = \$request->get(\$value);
                    }
                }
                $modelData->tableName::{$whereClause}update(\$toUpdate);
                return ["Success" => true]; 
            }
            text;
            return $toReturn;
        }
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
                return $toReturn;
            }
    }
    
    public function delete($requestData, $fields, $modelData){
        foreach($requestData as $value){
            $functionTop = $this->getFunctionParams($value);

            $whereClause = $this->getRouteParam($value->request->route)["whereClause"];
            $toReturn = <<<text
            $functionTop{
                $modelData->tableName::{$whereClause}delete();
                return ["Success" => true];
            }
            text;
            return $toReturn;
        }
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