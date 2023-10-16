<?php
namespace App\Controllers;

class Controller
{
    protected static $r_instance;
    protected $request_uri;
    protected $request_method;
    public $get;
    public $post;


    public function __construct() {
        $this->request_uri = $_SERVER['REQUEST_URI'];
        $this->request_method = $_SERVER['REQUEST_METHOD'];
        $this->get = $_GET;
        $this->post = $_POST;
    }
    public static function init()
    {
        if (is_null(self::$r_instance))
            self::$r_instance = new Controller();
        return self::$r_instance;
    }

}