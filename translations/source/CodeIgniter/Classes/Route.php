<?php 
class Route{

    public function __construct($jsonData){
        $this->jsonInput = $jsonData;
        $this->routeTo = ["view", "add", "edit", "delete"];
        $this->filePath = PROJECT_PATH . "/app/Config/Routes.php";
        $this->processRoute();
    }

    public function processRoute(){
        foreach( $this->jsonInput->data as $data ) {
            $this->processEach($data);
        }
    }

    public function processEach( $modelData ){
        $toWrite = file_get_contents( $this->filePath );
        foreach($this->routeTo as $i){
            foreach($modelData->model->$i as $j){

                $type = strtolower($j->request->type);
                $route = $j->request->route;
                $methodName = $j->request->name;
                $route = preg_replace('/{.*?}/i', "(:any)", $route);
                //remove if already defined in file
                $regex = "\$routes->get\(['\"]{$route}['\"], {0,}['\"]$modelData->controller::{$methodName}['\"]\);";
                $toWrite = preg_replace('/$regex/s', '', $toWrite);
                
                $toWrite .= "\n\$routes->$type('$route','$modelData->controller::$methodName');\n";
            }
        }
        file_put_contents($this->filePath, $toWrite);
    }

}

?>