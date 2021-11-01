<?php
include_once 'Builder.php';

/**
 * Class: Application Builder
 * Description: Build the complete Project
 * @Author : Samyam Kafle / Niraj Ghimire 
 * Copyright: 2021
 */

class Config_builder extends Builder
{
    public function __construct($config, $mapping)
    {
        $this->_config = $config;
        $this->set_mapping_keys($mapping);

    }

    public function build()
    {
        switch ($this->_config['config']['programming-language'])
        {
            case 'laravel':
                $this->built_laravel_project();
                break;
            case 'codeigniter':
                $this->built_ci_project();
                break;
            default:
                break;
        }
    }

    public function built_laravel_project ()
    {
        $template = file_get_contents(__DIR__ . '/source/laravel/config.php');

        $config_array = "\n";
        
        foreach ($this->_config['config'] as $key => $value)
        {
            if (!is_array($value))
            {
                if (is_string($value))
                {
                    $config_array .= "\t\t\t\"$key\"" . "\t=>\t" . "\"$value\"" . ",\n";
                }
                else
                {
                    $config_array .= "\t\t\t\"$key\"" . "\t=>\t" . $value . ",\n";
                }
            }
            else
            {
                $config_array .= "\t\"$key\"\t=>[\n";
                $array_values_string = '';
                $config_array .= $this->_addArrayValues($array_values_string, $value);
                $config_array .= "],\n";
            }
        }
        $final_dir = '../release/core/';
        $final_location = $final_dir . 'config.php';
        if( !is_dir( $final_dir ) )
        {
            mkdir($final_dir);
        }

        $template = $this->inject_substitute_string ($template, 'CONFIG', $config_array);
        $this->_copy[$final_location] = $template;
        $this->_includes[] = 'core/config.php';
    }
    public function built_ci_project ()
    {
        //TODO: For CI
    }
    private function _addArrayValues($template, $array)
    {
        foreach ($array as $key => $value)
        {
            if (!is_array($value))
            {
                if (is_string($value))
                {
                    $template .= "\"$key\"" . "\t=>\t" . "\"$value\"" . ",\n";
                }
                else
                {
                    $template .= "\"$key\"" . "\t=>\t" . $value . ",\n";
                }
            }
            else
            {
                $template .= "\"$key\"\t=>[\n";
                $array_values_string = '';
                $template .= $this->_addArrayValues($array_values_string, $value);
                $template .= "\n]";
            }
        }
        return $template;
    }
}
