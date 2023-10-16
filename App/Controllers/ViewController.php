<?php
namespace App\Controllers;

class ViewController extends Controller
{
    protected static $r_instance;

    public static function init()
    {
        if (is_null(self::$r_instance))
            self::$r_instance = new ViewController();
        return self::$r_instance;
    }

    public function Page($filename=null,$dir=null)
    {
        $patch=$dir?$dir.'/'.$filename:$filename;
        include $_SERVER['DOCUMENT_ROOT'].'/App/Views/'.$patch.'.php';
    }
}