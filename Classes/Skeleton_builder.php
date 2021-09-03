<?php 
class Skeleton_builder {

    protected $mapping; 

    public function __construct(){

    }

    public function get_mapping_markup( $mapping_data )
    {
        
        $mapping_template = "";
        $mapping_template .= "\tpublic function {{{mapping_name}}}_mapping() \n \t{ \n";
        $mapping_template .= " \t\treturn [ \n " ;
        if( is_array( $mapping_data ) && !empty( $mapping_data ) ) 
        {
            foreach( $mapping_data as $key => $value )
            {
                $mapping_template .= "\t\t\t\"{$key}\" => \"{$value}\",\n";
            }
        }
        $mapping_template .= "\n\t\t]; \n \t} \n";
        return $mapping_template;
    } 

}