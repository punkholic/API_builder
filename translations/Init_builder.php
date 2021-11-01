<?php
include_once 'Builder.php';
/**
 * Class: Application Builder
 * Description: Build the complete Project
 * @Author : Samyam Kafle / Niraj Ghimire 
 * Copyright: 2021
 */
class Init_builder extends Builder
{
    protected $_config;

    /**
     * Constructor
     *
     * @param [type] $config
     * @param [type] $mapping
     */
    public function __construct($config, $mapping)
    {
        $this->_config = $config;
        $this->set_mapping_keys($mapping);

    }

    /**
     * Build
     *
     * @return void
     */
    public function build()
    {
        if (!isset($this->_config['config']))
        {
            throw new Exception('Setting Object Missing', 1);
        }

        $settings = $this->_config['config'];

        if (!isset($settings['programming-language']))
        {
            throw new Exception('Programming Language Missing', 1);
        }

        $programming_language = $settings['programming-language'];

        switch ($programming_language) {
            case 'laravel':
                $this->recurse_copy('../initialize_laravel', '../release');
                break;
            default:
                throw new Exception('Invalid Programming Language', 1);
                break;
        }
    }

    /**
     * Recursive Copy folders
     *
     * @param [type] $src
     * @param [type] $dst
     * @param string $childFolder
     * @return void
     */
    public function recurse_copy($src, $dst, $childFolder = '')
    {
        $dir = opendir($src);

        if (!is_dir($dst))
        {
            mkdir($dst);
        }

        if ($childFolder != '')
        {
            mkdir($dst .'/' . $childFolder);

            while(false !== ( $file = readdir($dir)) )
            {
                if (( $file != '.' ) && ( $file != '..' ))
                {
                    if ( is_dir($src . '/' . $file) )
                    {
                        $this->recurse_copy($src . '/' . $file, $dst . '/' . $childFolder . '/' . $file);
                    }
                    else
                    {
                        copy($src . '/' . $file, $dst . '/' . $childFolder . '/' . $file);
                    }
                }
            }
        }
        else
        {
            while(false !== ( $file = readdir($dir)) )
            {
                if (( $file != '.' ) && ( $file != '..' ))
                {
                    if ( is_dir($src . '/' . $file) )
                    {
                        $this->recurse_copy($src . '/' . $file,$dst . '/' . $file);
                    }
                    else
                    {
                        copy($src . '/' . $file, $dst . '/' . $file);
                    }
                }
            }
        }
        closedir($dir);
    }
}