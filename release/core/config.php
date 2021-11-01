<?php

class Config
{
  private static $instance = null;
  protected $_config = [];

  public function __construct ()
  {
    $this->_config = [

			"app_name"	=>	"test",
			"programming-language"	=>	"laravel",
			"mode"	=>	"development",

    ];
  }

  /**
   * Get Instance
   *
   * @return mixed
   */
  public static function get_instance()
  {
    if (self::$instance == null)
    {
      self::$instance = new Config ();
    }

    return self::$instance;
  }

  /**
   * Get Connection
   *
   * @return mixed
   */
  public function get_config ()
  {
    return $this->_config;
  }

}