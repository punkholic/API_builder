<?php

class Migration{

    public function __construct($jsonData){
        $this->jsonInput = $jsonData;
        $this->filePath = PROJECT_PATH . "/app/Database/Migrations/";
        $this->changeDatabase();
        $this->migrations = $this->getFileList();
        $this->processModel();
        chdir(PROJECT_PATH);
        // echo shell_exec( Constant::COMMANDS['MIGRATE_FRESH'] );
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
        foreach( $this->jsonInput->data as $data ){
            if( array_key_exists( strtolower( $data->tableName ), $this->migrations )){
                echo "Ignoring table $data->tableName, reason: already exist If you have any additions please drop the table from your side and run migrations.\n";
                continue;
            }
            $this->processEach($data);
        }
    }
    private function processEach( $modelData ){
        $tableName = strtolower( $modelData->tableName );
        $phaseOne = str_replace("PLACEHOLDER1", $tableName, Constant::COMMANDS['MAKE_MIGRATION']);
        $secondPhase = str_replace("PLACEHOLDER2", $modelData->tableName, $phaseOne);
        chdir(PROJECT_PATH);
        echo shell_exec($secondPhase);

        //generate code for recently created file
        $this->insertField( $tableName, $this->processFields( $modelData->model->fields, $tableName, $modelData->model->timestamps ) );
    }

    public function changeDatabase(){
        $changeData = [
            "database" => "test",
            "username" => "root",
            "password" => "root",
            "hostname" => "localhost",
        ];
        $fileToChange = $this->filePath . "../../Config/Database.php";
        $initialData = file_get_contents( $fileToChange );
        foreach($changeData as $key => $value){
            $initialData = preg_replace("/'$key'[ ]+=>[ ]+'[a-zA-Z0-9]{0,}',/", "'$key' => '$value',", $initialData);
        }
        file_put_contents($fileToChange, $initialData);
    }
    private function insertField($tableName, $toWrite){
        //re-rendering files to get newly created files in migrations array
        
        $foundFiles = $this->getFileList();
        
        $fileKey = "";
        foreach($foundFiles as $key => $value ){
            if(strpos(strtolower($key), strtolower($tableName))) $fileKey = $value;
        }

        $fileName = $this->filePath . $fileKey;
        $initialData = file_get_contents( $fileName );
        
        $upData = $toWrite['up'];
        $downData = $toWrite['down'];

        $upCode = <<<DOC
        public function up()
        \t{
            $upData
        }
        DOC;
        $downCode = <<<DOC
        public function down()
        \t{
            $downData
        }
        }
        DOC;
        // //using regex to replace fields
        $firstPhase = preg_replace('/public function up(.*)[\n \w\W]+?}.*[\n \w\W]+?}?/', $upCode, $initialData);
        $finalPhase = preg_replace('/public function down(.*)[\n \w\W]+?}.*[\n \w\W]+?}/', $downCode, $firstPhase);
        file_put_contents($fileName, $finalPhase);
    }
    private function processFields($fields, $tableName, $hasTimestamp){
        $timestampString = "
            'created_date datetime default current_timestamp',
            'updated_date datetime default current_timestamp on update current_timestamp',
        ";
        $typeChange = [
            "primary" => "[
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ]",
            "string" => "[
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ]",
            "integer" => "[
                'type'           => 'INT',
                'constraint'     => 5,
            ]",
            "text" => "[
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ]",
            "decimal" => "[
                'type' => 'DECIMAL',
                'constraint' => '10,2',
            ]",
            "hash" => "[
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ]",
            "date" => "[
                'type' => 'date',
            ]",
            "datetime" => "[
                'type' => 'datetime',
            ]",
            "enum" => "[
                'type' => 'ENUM',
                'constraint' => ['1', '0'],
                'default' => '1',
            ]",
        ];
        
        $options = [
            "required" => [
                'null' => true,
            ],
        ];

        $gotValues = "\$this->forge->addField([\n";
        $primary = "id";

        foreach($fields as $key => $value){
            $tempValue = $value;
            $optionalInfo = explode("|", $value);
            if ($value == "primary") $primary = $key;

            $value = $optionalInfo[0];
            $toPut = $typeChange[$value];

            if(!in_array("required", $optionalInfo)){
                $gotData = explode("\n", $toPut);
                $lastIndex = count($gotData) - 1;
                $tempData = $gotData[$lastIndex];
                $gotData[$lastIndex] = "\t\t'null' => true,";
                $gotData[] = $tempData;
                $toPut = implode("\n", $gotData);
            }

            $gotValues .= "\t'$key' => $toPut,\n";
        }
        if($hasTimestamp) $gotValues .= $timestampString;
        $gotValues .= "]);\n
            \$this->forge->addKey('$primary', true);
            \$this->forge->createTable('$tableName');
        ";
        $toReturn['up'] = $gotValues;
        $toReturn['down'] = "\t\$this->forge->dropTable('$tableName');";

        return $toReturn;
    }
}

?>