<?php
include_once 'Builder.php';
include_once 'Init_builder.php';
include_once 'Include_builder.php';
include_once 'Config_builder.php';
include_once 'Copy_builder.php';

/**
 * Class: Application Builder
 * Description: Build the complete Project
 * @Author : Samyam Kafle / Niraj Ghimire 
 * Copyright: 2021
 */
class Application_builder extends Builder
{
    protected $_config;
    protected $_render_list = [];
    protected $_models = [];
    protected $_includes = [];
    protected $_copy = [];
    protected $_middleware = [];
    protected $_mapping_keys = [];


    /**
     * Constructor
     *
     * @param [type] $config
     */
    public function __construct( $config ) 
    {
        $this->_config = json_decode( $config , TRUE );
        $this->_mapping_keys = isset($this->_config['mapping']) ? $this->_config['mapping'] : [];

    }

    /**
     * Init Function
     *
     * @return void
     */
    public function init()
    {
        $init_builder = new Init_builder( $this->_config, $this->_mapping_keys );
        $this->_render_list[] = $init_builder;

        $config_builder = new Config_builder($this->_config, $this->_mapping_keys );
        $this->_render_list[] = $config_builder;

        
    }

    /**
     * Build Function
     *
     * @return void
     */
    public function build()
    {
        $this->init();

        foreach ($this->_render_list as $builder_name => $builder)
        {
            $builder->build();
            $this->_includes = array_merge($this->_includes, $builder->get_includes());
            $this->_copy = array_merge($this->_copy, $builder->get_copy());
            $this->_mapping_keys = array_merge($this->_mapping_keys, $builder->get_mapping_keys());

        }

        $copy_builder = new Copy_builder($this->_config, $this->_mapping_keys);
        $copy_builder->set_copy($this->_copy);
        $copy_builder->build();

        $include_builder = new Include_builder($this->_config, $this->_mapping_keys);
        $include_builder->set_includes($this->_includes);
        $include_builder->build();
        

    }

    /**
     * Destroy Function
     *
     * @return void
     */
    public function destroy()
    {
        recursive_remove('../release');
    }
}


/**
 * Recursive remove files
 *
 * @param [type] $dir
 * @return void
 */
function recursive_remove($dir)
{
    if ($handle = opendir($dir))
    {
        while (( $file = readdir($handle)) !== false )
        {
            if ($file != '.' && $file != '..')
            {
                system('rm -rf ' . escapeshellarg($dir . '/' . $file));
            }
        }
    }
    closedir($handle);
}