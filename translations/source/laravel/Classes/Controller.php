<?php
class Controller{

    public function __construct($jsonData){
        $this->jsonInput = $jsonData;
        $this->filePath = PROJECT_PATH . "/app/Http/Controllers/";
        $this->toProcess = ["view", "edit", "add", "delete" ];
        $this->processModel();
    }
    
    public function processModel(){
        chdir(PROJECT_PATH);
        foreach($this->jsonInput->data as $data){
            shell_exec(Constant::COMMANDS['MAKE_CONTROLLER'] . " " . $data->controller );
            $this->processEach($data);
        }
    }

    public function processEach($modelData){
        $this->controllerCode = file_get_contents("$this->filePath$modelData->controller.php");
       
        foreach($this->toProcess as $value){
            $this->$value($modelData->model->$value, $modelData->model->fields, $modelData) . "\n\n";
        }
        $this->setControllersHeadings($modelData->tableName);
        file_put_contents("$this->filePath$modelData->controller.php", $this->controllerCode);
    }
    public function setControllersHeadings($tableName){
        $toCheck = [
            "use Illuminate\\\Http\\\Request;",
            "use Illuminate\\\Support\\\Facades\\\Hash;"
        ];

        $databaseModel = "use App\\\\Models\\\\{$tableName};" ;
        
        if(!preg_match("/$databaseModel/i", $this->controllerCode)){
            $toInsert = "\n\n$databaseModel\n\n class";
            $this->controllerCode = preg_replace("/[\n ]+class/i", $toInsert, $this->controllerCode);
        }

        foreach($toCheck as $value){
            if(!preg_match("/$value/i", $this->controllerCode)){
                $toInsert = "\n\n$value\n class";
                $this->controllerCode = preg_replace("/[\n ]+class/i", $toInsert, $this->controllerCode);
            }
        }
    }
    public function getArrayString($arrayName, $array, $type, $named=true){
        $toReturn = "\$$arrayName = [";
        if(!$named){
            $toReturn = "[";
        }
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
        $toReturn .= "]";

        if($named){
            $toReturn .= ";";
        }
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

    public function replaceFunction($functionName, $toChange){
        $regex = '[ ]{0,}public[ \n]+function[ \n]+' . $functionName . '[\d\D]+?return[\d\D]+?}';
        if(preg_match("/$regex/", $this->controllerCode)){
            $this->controllerCode = preg_replace("/$regex/", $toChange, $this->controllerCode);
        }else{
            $toChange = "extends Controller \n{\n" . $toChange;
            $this->controllerCode = preg_replace("/extends[ ]+Controller[\n \t]+{/", $toChange, $this->controllerCode);
        }
    }

    
    public function view($requestData, $fields, $modelData){
        $allFields = array_keys((array)$fields);
        
        foreach($requestData as $value){
            $functionTop = $this->getFunctionParams($value);

            $whereClause = $this->getRouteParam($value->request->route)["whereClause"];
            $selectData = "'*'";
            $validFields = (array_intersect($allFields, $value->fields));
            if(!empty($validFields)){
                $selectData = "";
                foreach($validFields as $data){
                    $selectData .= "'$data', ";
                }
                $selectData = substr($selectData, 0, strlen($selectData) - 2);
            }

            $toReturn = <<<text
            $functionTop{
                \$data =  $modelData->tableName::{$whereClause}select($selectData)->get();
                return \$data;
            }
            text;
            $this->replaceFunction($value->request->name, $toReturn);

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

    
    public function generateHashCode($fieldsInfo, $isEdit=false){
        $variableName = $isEdit ? "\$toUpdate" : "\$toStore";
        $requiredHash = array_intersect($fieldsInfo['hash'], $fieldsInfo['required']);
        $optionalHash = array_intersect($fieldsInfo['hash'], $fieldsInfo['optional']);

        if($isEdit){
            $hashes = $optionalHash + $requiredHash;
            $allHash = $this->getArrayString("", $hashes, "list", false);
            if(count($hashes) == 0){
                return "";
            }
            
            return <<<text
                if (in_array(\$value, $allHash)) {
                    \$toUpdate[\$value] = Hash::make(\$request->get(\$value));
                    continue;
                }
            text;
        }
        $optionalString = $this->getArrayString("", $optionalHash, "list", false);
        $requiredString = $this->getArrayString("", $requiredHash, "list", false);
        
        $optionalReturn = <<<text
        if(in_array(\$optionalField[\$i], $optionalString)){
            {$variableName}[\$optionalField[\$i]] = Hash::make(\$request->get(\$optionalField[\$i]));
            continue;
        }
        text;
        
        $requiredReturn = <<<text
        if(in_array(\$mustHave[\$i], $requiredString)){
            {$variableName}[\$mustHave[\$i]] = Hash::make(\$request->get(\$mustHave[\$i]));
            continue;
        }
        text;       

        if(count($optionalHash) == 0){
            $optionalReturn = "";
        }
        if(count($requiredHash) == 0){
            $requiredReturn = "";
        }
       return ["optionalHash" => $optionalReturn, "requiredHash" => $requiredReturn];
    }

    public function generateUploadCode($fieldsInfo, $isEdit = false){
        $requiredImage = array_intersect($fieldsInfo['image'], $fieldsInfo['required']);
        $optionalImage = array_intersect($fieldsInfo['image'], $fieldsInfo['optional']);
        
        $allImages = $requiredImage + $optionalImage;
        $editArray = $this->getArrayString("", $allImages, "list", false);
        if($isEdit){
            return <<<text
            foreach($editArray as \$value){
                if (\$request->file(\$value) != null) {
                    \$imageName = time().'.'.\$request->file(\$value)->extension();  
                    \$request->file(\$value)->move(public_path('img'), \$imageName);
                    \$nameToStore= "http://".request()->getHttpHost()."/img/\$imageName";
                    \$toUpdate[\$value] = \$nameToStore;
                    continue;
                }
            }
            text;
        }

        // $optionalArray = $this->getArrayString("", $optionalImage, "list", false);
        $imageData = $this->getArrayString("", $allImages, "list", false);
        
        foreach(["mustHave", "optionalField"] as $value){
            $$value = <<<text
            if (in_array(\${$value}[\$i], $imageData)) {
                \$imageName = time().'.'.\$request->file(\${$value}[\$i])->extension();  
                \$request->file(\${$value}[\$i])->move(public_path('img'), \$imageName);
                \$nameToStore= "http://".request()->getHttpHost()."/img/\$imageName";
                \$toStore[\${$value}[\$i]] = \$nameToStore;
                continue;
            }
            text;
        } 
 
       
        if(count($requiredImage) == 0){
            $mustHave = "";
        }
        if(count($optionalImage) == 0){
            $optionalField = "";
        }
        return ["requiredImage" => $mustHave, "optionalImage" => $optionalField];
    }

    public function add($requestData, $fields, $modelData){
        $fieldsInfo = $this->getFieldTypes($fields);
        foreach($requestData as $value){
            $functionTop = $this->getFunctionParams($value);
            
            $optionalFields = array_intersect($value->fields, $fieldsInfo['optional']);
            $requiredFields = array_intersect($value->fields, $fieldsInfo['required']);
            
            $optionalString = $this->getArrayString("optionalField", $optionalFields, "list");
            $requiredString = $this->getArrayString("mustHave", $requiredFields, "list");
            
            
            $additionalImageCode = '';
            $additionalHashCode = ['requiredHash' => '', 'optionalHash' => ''];
            if ( array_key_exists('hash', $fieldsInfo) ) {
                $additionalHashCode = $this->generateHashCode($fieldsInfo);
            }
            if(array_key_exists('image', $fieldsInfo)){
                $additionalImageCode = $this->generateUploadCode($fieldsInfo);
            }
            $toReturn = <<<text
                $functionTop { 
                    $optionalString
                    $requiredString
                    \$toValidate = [];
                    \$toStore = [];
                    for(\$i = 0; \$i < count(\$mustHave); \$i++){
                        \$toValidate[\$mustHave[\$i]] = ['required'];
                        {$additionalHashCode['requiredHash']}
                        {$additionalImageCode['requiredImage']}
                        \$toStore[\$mustHave[\$i]] = \$request->get(\$mustHave[\$i]);
                    }
                    for(\$i = 0; \$i < count(\$optionalField); \$i++){
                        {$additionalHashCode['optionalHash']}
                        {$additionalImageCode['optionalImage']}
                        \$toStore[\$optionalField[\$i]] = \$request->get('img');
                    }
                    $modelData->tableName::insert(\$toStore);
                    return ["Success" => true]; 
                }
                text;
                $this->replaceFunction($value->request->name, $toReturn);
            }
    }
    public function edit($requestData, $fields, $modelData){
        $fieldsInfo = $this->getFieldTypes($fields);
        foreach($requestData as $value){

            $functionTop = $this->getFunctionParams($value);
            $updateFieldString = $this->getArrayString("updateFields", $value->fields, "list");
            $whereClause = $this->getRouteParam($value->request->route)["whereClause"];
            
            $hashCode = '';

            if ( array_key_exists('hash', $fieldsInfo) ) {
              $hashCode = $this->generateHashCode($fieldsInfo, true);
            }
            if ( array_key_exists('image', $fieldsInfo) ) {
                $additionalImageCode = $this->generateUploadCode($fieldsInfo, true);
              }

            
            $toReturn = <<<text
            $functionTop
            {
                $updateFieldString
                \$toUpdate = [];
                $additionalImageCode
                foreach(\$updateFields as \$value){
                    if(\$request->get(\$value) != null){
                        $hashCode
                        \$toUpdate[\$value] = \$request->get(\$value);
                    }
                }
                $modelData->tableName::{$whereClause}update(\$toUpdate);
                return ["Success" => true]; 
            }
            text;
            $this->replaceFunction($value->request->name, $toReturn);
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
            $this->replaceFunction($value->request->name, $toReturn);
        }
    }

    public function getFieldTypes($data){
        $toReturn = ["required" => [], "optional" => []];
        foreach($data as $key => $value){
            if (strpos($value, "required") !== false || strpos($value, "primary") !== false){
                $toReturn["required"][] = $key;
            }else{
                $toReturn["optional"][] = $key;
            }

            if (strpos($value, "hash") !== false){
                $toReturn["hash"][] = $key;
            }else if(strpos($value, "image") !== false){
                $toReturn["image"][] = $key;
            }
            
        }
        return $toReturn;
    }
}
?>
