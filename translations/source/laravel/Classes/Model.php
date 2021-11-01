<?php 
include_once("Skeleton_builder.php");

class Model{

    public function __construct($input){
        $this->jsonInput = $input;
        $this->filePath = PROJECT_PATH . "/app/Models/";
        $this->processModel();
    }

    public function processModel(){
        foreach($this->jsonInput as $data){
            $this->processEach($data);
        }
    }
   
    public function processEach($modelData){
      
        $exist = glob( $this->filePath . $modelData->tableName . ".php");
        if( $exist )
        {
            unlink( $this->filePath . $modelData->tableName . ".php" );
        }
      
        $undefined = [];
        foreach( $modelData->model->fields as $key => $value){
           
            if( ! in_array( $key, $modelData->model->fillable ) && ! in_array( $key, $modelData->model->guarded ) ){
                array_push( $undefined, $key );
            }
        }
       

        
        

        //creating model
        echo shell_exec("cd ". PROJECT_PATH . " && " . Constant::COMMANDS['MAKE_MODEL'] . " $modelData->tableName");
        
        $fillable = array_merge($modelData->model->fillable, $undefined);

        // $insertingText = "\n\tprotected \$table = \n\n";
        $insertingText = "\n\tprotected \$fillable = ['" . implode("','", $fillable) . "'];\n\n";
        $insertingText .= "\n\tprotected \$guarded = ['" . implode("','", $modelData->model->guarded) . "'];\n\n";

        /**
         * Create mapping if exists
         */

        $skeleton_builder = new Skeleton_builder();

        if( is_array( $modelData->model->mapping ) && count( $modelData->model->mapping ) > 0 )
        {
            $all_maping = "";

            foreach( $modelData->model->mapping as $m_value){
               foreach( $m_value as $main_key => $main_value )
               {
                    
                    $final_string_mapping = array() ; 
                    foreach( $main_value as $key => $value )
                    {
                        $final_string_mapping[ $key ] = $value;

                    }
                   
                    if( is_array( $final_string_mapping ) && !empty( $final_string_mapping ) )
                    {
                        $mapping_skeleton = $skeleton_builder->get_mapping_markup( $final_string_mapping );
                        $new_fun_dynamic = str_replace( "{{{mapping_name}}}", $main_key ,$mapping_skeleton );

                    }
                    
                    $all_maping .= $new_fun_dynamic;
               }
            
            }
            $insertingText .= $all_maping;

        }
     
        /**
         * Class closing tag 
         * Should always be at the end
         */
        $insertingText .= "}";

        $gotFileData = file_get_contents($this->filePath . $modelData->tableName . ".php");
        $tableName = "\n\tprotected \$table = '$modelData->tableName';\n\n}";
        //removing pre-define content (if exist)
        $toInsert = preg_replace('/protected \$(fillable|guarded)(.*);/s', '', $gotFileData);
        $toInsert = preg_replace('/[ \t]{0,}protected[ \t]{0,}\$table[ \t]{0,}=[ \t]{0,}\'(.*)\'[ \t]{0,};/', $tableName, $gotFileData);
        if(!preg_match('/\$table/', $gotFileData)){
            $toInsert = preg_replace('/\n}/s', $tableName, $toInsert);
        }
        $toInsert = preg_replace('/\n}/s', $insertingText, $toInsert);

        //writing to file
        file_put_contents($this->filePath . $modelData->tableName . ".php", $toInsert);
    }
}

?>