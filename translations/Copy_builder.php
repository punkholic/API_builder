<?php
include_once 'Builder.php';

/**
 * Class: Application Builder
 * Description: Build the complete Project
 * @Author : Samyam Kafle / Niraj Ghimire 
 * Copyright: 2021
 */

class Copy_builder extends Builder
{
    protected $_config;

    public function __construct($config, $mapping)
    {
        $this->_config = $config;
        $this->set_mapping_keys($mapping);
    }

    public function build()
    {
        foreach ($this->get_copy() as $template => $value)
        {
            foreach ($this->get_mapping_keys() as $mapping_key => $mapping_value)
            {
                if (!is_array($mapping_value))
                {
                    if (strpos($mapping_key, '//') === 0)
                    {
                        $value = str_replace($mapping_key, $mapping_value, $value);
                    }
                    else
                    {
                        $value = str_replace('{{{' . $mapping_key . '}}}', $mapping_value, $value);
                    }
                }
            }

            file_put_contents($template, $value);
        }
    }
}