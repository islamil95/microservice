<?php
namespace App\Controllers;

Class FilterController extends Controller {
    protected static $r_instance;
    public $filter=" vin!='' ";
    public function __construct()
    {
        if(isset(Controller::init()->get["mark"]))
        {
            $this->filter.=" AND mark_id='".Controller::init()->get["mark"]."'";
        }

        if (isset(Controller::init()->get["address"])) {
            $addressParam = Controller::init()->get["address"];
            $addresses = explode(';', $addressParam);
            $addressFilter = '';

            foreach ($addresses as $index => $address) {
                if ($index == count($addresses) - 1) {
                    $addressFilter .= "'".$address."'";
                } else {
                    $addressFilter .= "'".$address."',";
                }
            }
            $this->filter .= " AND dealer_center_guid IN (".$addressFilter.")";
        }
    }
    public static function init()
    {
        if (is_null(self::$r_instance))
            self::$r_instance = new FilterController();
        return self::$r_instance;
    }
}