<?php
namespace App\Controllers;

use App\Models\CarModel;
use SimpleXMLElement;

class FeedNewExeedPerxController
{
    public function index()
    {
        if(empty(Controller::init()->get["mark"]))
        {
           FilterController::init()->filter.=" AND mark_id='EXEED'";
        }
        if(empty(Controller::init()->get['format']) || Controller::init()->get['format']=='xml'){
            $this->getXmlNewCar();
        }
    }

    public function getXmlNewCar()
    {
        $xml = new SimpleXMLElement('<data></data>');
        $carsElement = $xml->addChild('cars');

        foreach (CarModel::init()->getCar('new_cars',FilterController::init()->filter) as $car) {

            $mark_id=CarModel::init()->removeSpaces(CarModel::init()->capitalizeFirstLetter($car['mark_id']));
            $folder_id=CarModel::init()->folderId($car['folder_id']);
            $modification_id=CarModel::init()->modificationId($car['complectation_name'],$car['engine_power'],$car['folder_id']);
            $complectation_name=CarModel::init()->complectationName($car['complectation_name'],$car['folder_id']);
            $color=CarModel::init()->color($car['color']);

            if($mark_id && $folder_id && $modification_id && $complectation_name && $color && isset($car['complectation_name']) && isset($car['engine_power']) && $car['reserve']!==1) {
                $carElement=$carsElement->addChild('car');
                $carElement->addChild('mark_id', $mark_id);
                $carElement->addChild('folder_id', $folder_id);
                $carElement->addChild('modification_id', $modification_id);
                $carElement->addChild('complectation_name', $complectation_name);
                $carElement->addChild('color', $color);
                $carElement->addChild('year', $car['year']?:"");
                $carElement->addChild('vin', $car['vin']?:"");
                $carElement->addChild('price', $car['price']?:"");
            }
        }
        header('Content-Type: application/xml');

        // Выводим XML
        echo $xml->asXML();
    }
}