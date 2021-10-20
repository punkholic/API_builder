<?php

class Migration{

    public function __construct($jsonData){
        $this->jsonInput = $jsonData;
        $this->filePath = PROJECT_PATH . "/database/migrations/";
        $this->migrations = $this->getFileList();
        $this->processModel();
        echo shell_exec("cd " . PROJECT_PATH . " && " . Constant::COMMANDS['MIGRATE_FRESH']);
    }
    
    private function getFileList(){

        //get files from respective dir
        $files = glob( $this->filePath . "*.php" );
        
        // mapping to filter last part of the file and returning it
        $toReturn = [];
        foreach($files as $data){
            $explodedPath = explode( '/', $data );
            $value = $explodedPath[ count($explodedPath) - 1 ];

            //removing unwanted strings from file name
            $key = preg_replace('/([0-9]+_(create_|))+/s', '', $value);
            $key = str_replace("_table.php", "", $key);
            $toReturn[$key] = $value;
        }
       
        return $toReturn;
    }

    private function processModel(){
        foreach( $this->jsonInput as $data ){
            if( array_key_exists( strtolower($data->tableName), $this->migrations )){
                echo "Ignoring table $data->tableName, reason: already exist If you have any additions please drop the table from your side and run migrations.\n";
                continue;
            }
            $this->processEach($data);
        }
    }

    private function processEach($modelData){
        $tableName = strtolower( $modelData->tableName );
        $phaseOne = str_replace("PLACEHOLDER1", $tableName, Constant::COMMANDS['MAKE_MIGRATION']);
        $secondPhase = str_replace("PLACEHOLDER2", $modelData->tableName, $phaseOne);
        shell_exec("cd " . PROJECT_PATH . " && $secondPhase");

        //generate code for recently created file
        $this->insertField( $tableName, $this->processFields( $modelData->model->fields, $tableName ) );
    }

    private function insertField($tableName, $toWrite){
        //re-rendering files to get newly created files in migrations array
        $foundFiles = $this->getFileList();
        $fileName = $this->filePath . $foundFiles[$tableName];
        $initialData = file_get_contents( $fileName );

        //using regex to replace fields
        preg_match('/public function up(.*)[\n \w\W]+?}.*[\n \w\W]+?}/', $initialData, $matches);
        $firstPhase = $matches[0];
        $secondPhase = preg_replace("/Schema(.*)[\n \w\W]+?}\);/s", $toWrite, $firstPhase);
        $finalPhase = preg_replace('/public function up(.*)[\n \w\W]+?}.*[\n \w\W]+?}/', $secondPhase, $initialData);

        file_put_contents($fileName, $finalPhase);

    }
    private function processFields($fields, $tableName){
        $typeChange = [
            "primary" => "increments('PASSED_DATA')",
            "string" => "string('PASSED_DATA')",
            "integer" => "integer('PASSED_DATA')",
            "text" => "text('PASSED_DATA')",
            "decimal" => "decimal('PASSED_DATA', 8, 2)",
            "hash" => "string('PASSED_DATA')",
            "date" => "date('PASSED_DATA')",
            "datetime" => "dateTime('PASSED_DATA')",
            "enum" => "string('PASSED_DATA', 1)"
        ];
        $options = [
            "required" => "->nullable()",
        ];
        $gotValues = "";
        
        foreach($fields as $key => $value){
            $tempValue = $value;
            $optionalInfo = explode("|", $value);
            $value = $optionalInfo[0];
            $tableMethod = str_replace( "PASSED_DATA", $key, $typeChange[$value] );
            
            $gotValues .=  "\$table->$tableMethod";
            if(strpos($value, "primary") === false){
                foreach($options as $option => $value){
                    if(!in_array($option, $optionalInfo)){
                        $gotValues .= $value;
                    }
                }
            }
            $gotValues .= ";\n";
        }

        $toReturn = "Schema::create('$tableName', ";
        $toReturn .= Common::renderFunction([
            "text" => $gotValues,
            "tabs" => 3,
            "params" => "Blueprint \$table"
        ]);

        $toReturn .= ");";
        return $toReturn . "\n";
    }
}

?>