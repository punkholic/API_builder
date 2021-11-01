<?php 
class Route{

    public function __construct($jsonData){
        $this->jsonInput = $jsonData;
        $this->routeTo = ["view", "add", "edit", "delete"];
        $this->filePath = PROJECT_PATH . "/routes/api.php";
        $this->processRoute();
    }

    public function processRoute(){
        foreach($this->jsonInput as $data){
            $this->processEach($data);
        }
    }

    public function processEach($modelData){

        $toWrite = file_get_contents($this->filePath);

        foreach($this->routeTo as $i){
            foreach($modelData->model->$i as $j){

                $type = strtolower($j->request->type);
                $route = $j->request->route;
                $methodName = $j->request->name;

                //remove if already defined in file
                $regex = "Route::$type\(('|\")\\$route('|\")(.*);";
                $toWrite = preg_replace('/$regex/s', '', $toWrite);
                
                // overwrite or add new one
                $toWrite .= "\nRoute::$type('$route','App\Http\Controllers\\$modelData->controller@$methodName');\n";
            }
        }
        file_put_contents($this->filePath, $toWrite);
    }

}

?>