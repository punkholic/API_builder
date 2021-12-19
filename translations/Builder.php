<?php
/** 
 * @author: Samyam Kafle / Niraj Ghimire 
 * email : samyam1kafle@gmail.com 
 * Copyright: 2021 
 * */ 
abstract class Builder
{
    protected $_config;
    protected $_copy = [];
    protected $_includes = [];
    protected $_middleware = [];
    protected $_mapping_keys = [];
    protected $_name = '';

    public function build ()
    {
    }

    public function destroy ()
    {
    }

    public function get_config ()
    {
        return $this->_config;
    }

    public function set_config ($config)
    {
        $this->_config = $config;
    }

    public function set_copy ($copy)
    {
        $this->_copy = $copy;
    }

    public function get_includes ()
    {
        return $this->_includes;
    }

    public function set_includes ($includes)
    {
        $this->_includes = $includes;
    }

    public function get_middleware ()
    {
        return $this->_middleware;
    }

    public function set_middleware ($middleware)
    {
        $this->_middleware = $middleware;
    }

    public function get_copy ()
    {
        return $this->_copy;
    }
    
    public function get_mapping_keys ()
    {
        return $this->_mapping_keys;
    }

    public function set_mapping_keys ($mapping_keys)
    {
        $this->_mapping_keys = $mapping_keys;
    }

    public function inject_substitute_string ( $text, $key, $value )
    {
        $text = str_replace('{{{'.$key.'}}}', $value, $text);
        foreach ($this->_mapping_keys as $t_key => $t_value) {

            $text = str_replace($t_key, $t_value, $text);
        }
        return $text;
    }

    public function inject_array($text, $key, $value)
    {
        foreach ($value as $k => $new_value)
        {
            if (is_string($new_value))
            {
                $value[$k] = "'$new_value'";
            }
        }

        $process_value = implode(',', $value);
        $value = str_replace('[', '', $process_value);
        $value = str_replace(']', '', $value);
        $text = str_replace('{{{'.$key.'}}}', 'array('.$value.')', $text);

        return $text;
    }

    public function printr($data)
    {
        echo '<pre>'.print_r($data, true).'</pre>';
    }


    protected function camelcase($string, $capitalizeFirstCharacter = false)
    {

        $str = str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));

        if (!$capitalizeFirstCharacter) {
            $str[0] = strtolower($str[0]);
        }

        return $str;
    }


}