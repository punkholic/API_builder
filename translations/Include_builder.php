<?php
include_once 'Builder.php';
/**
 * Class: Application Builder
 * Description: Build the complete Project
 * @Author : Samyam Kafle / Niraj Ghimire 
 * Copyright: 2021
 */
class Include_builder extends Builder
{
    protected $_config;

    /**
     * Constructor
     *
     * @param [type] $config
     */
    public function __construct($config, $mapping)
    {
        $this->_config = $config;
        $this->set_mapping_keys($mapping);

    }

    /**
     * Build Function
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

        switch ($programming_language)
        {
            case 'laravel':
                $this->build_laravel();
                break;
            default:
                throw new Exception('Invalid Programming Language', 1);
                break;
        }

    }

    private function process_include_php ($list)
    {
        foreach ($list as $key => $value)
        {
            $list[$key] = "require_once '$value';";
        }

        return $list;
    }


    private function build_laravel ()
    {
        $path = '../release/public/index.php';

        if (!file_exists($path))
        {
            throw new Exception("Initialize PHP not set");
        }

        $template = file_get_contents('../release/public/index.php');
        $include_code = implode("\n", $this->process_include_php($this->_includes));
        $middleware_code = implode("\n", $this->_middleware);
        $template = str_replace('// {{{INCLUDEFILES}}}', $include_code, $template);
        $template = str_replace('// {{{MIDDLEWARE}}}', $middleware_code, $template);
        file_put_contents('../release/public/index.php', $template);
    }

}