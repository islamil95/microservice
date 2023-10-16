<?php
namespace App\Controllers;

use App\Models\CarModel;
use SimpleXMLElement;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class FeedNewCarController
{
    public function index()
    {
        if(empty(Controller::init()->get['format']) || Controller::init()->get['format']=='xml'){
            $this->getXmlNewCar();
        }
        if(isset(Controller::init()->get['format']) && Controller::init()->get['format']=='excel'){
            $this->getExcelNewCar();
        }
    }

    public function getXmlNewCar()
    {
        $xml = new SimpleXMLElement('<data></data>');
        $carsElement = $xml->addChild('cars');

        foreach (CarModel::init()->getCar('new_cars',FilterController::init()->filter) as $car) {
            $carElement=$carsElement->addChild('car');
            $carElement->addChild('mark_id', $car['mark_id']?:"");
            $carElement->addChild('folder_id', $car['folder_id']?:"");
            $carElement->addChild('body_type', $car['body_type']?:"");
            $carElement->addChild('wheel', $car['wheel']?:"");
            $carElement->addChild('engine_volume', $car['engine_volume']?:"");
            $carElement->addChild('engine_type', $car['engine_type']?:"");
            $carElement->addChild('engine_power', $car['engine_power']?:"");
            $carElement->addChild('gearbox', $car['gearbox']?:"");
            $carElement->addChild('drive', $car['drive']?:"");
            $carElement->addChild('complectation_name', $car['complectation_name']?:"");
            $carElement->addChild('color', $car['color']?:"");
            $carElement->addChild('reserve', $car['reserve']?:"");
            $carElement->addChild('outer_color', $car['outer_color']?:"");
            $carElement->addChild('year', $car['year']?:"");
            $carElement->addChild('vin', $car['vin']?:"");
            $carElement->addChild('price', $car['price']?:"");
            $carElement->addChild('currency', $car['currency']?:"");
            $carElement->addChild('availability', $car['availability']?:"");
            $carElement->addChild('custom', $car['custom']?:"");
            $carElement->addChild('owners_number', $car['owners_number']?:"");
            $carElement->addChild('contact_info', $car['contact_info']?:"");
            $carElement->addChild('panoramas_autoru', $car['panoramas_autoru']?:"");
            $carElement->addChild('doors_count', $car['doors_count']?:"");
            $carElement->addChild('dealer_center_guid', $car['dealer_center_guid']?:"");
            $carElement->addChild('poi_id', $car['poi_id']?:"");
            $imageBig = $carElement->addChild('Images');
            $redImage=CarModel::init()->getImage($car['vin']);
            if($redImage){
                foreach ($redImage as $val){
                    $imageBig->addChild('image', $val['image']);
                }
            }

        }
        header('Content-Type: application/xml');

        // Выводим XML
        echo $xml->asXML();
    }

    public function getExcelNewCar()
    {
        // Создаем объект Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Заполняем заголовки
        $sheet->setCellValue('A1', 'Категория');
        $sheet->setCellValue('B1', 'Название');
        //$sheet->setCellValue('C1', 'Описание');
        $sheet->setCellValue('C1', 'Цена');
        $sheet->setCellValue('D1', 'Фото');
        $sheet->setCellValue('E1', 'Популярный товар');
        $sheet->setCellValue('F1', 'В наличии');

        // Заполняем данные
        $row = 2;
        foreach (CarModel::init()->getCar('new_cars',FilterController::init()->filter) as $car) {
            $sheet->setCellValue('A' . $row, 'Новые автомобили');
            $sheet->setCellValue('B' . $row, $car['mark_id'] . ' ' . $car['folder_id'] . ' ' . $car['engine_volume'] . ' ' . $car['year'] . 'г.в.');
      //    $sheet->setCellValue('C' . $row, $car['description']);
            $sheet->setCellValue('C' . $row, $car['price']);
            $getImg = CarModel::init()->getImage($car['vin']);
            $sheet->setCellValue('D' . $row, $getImg ? $getImg[0]['image'] : '');
            $sheet->setCellValue('E' . $row, 'Да');
            $sheet->setCellValue('F' . $row, $car['availability']);
            $row++;
        }

        // Создаем объект Writer для сохранения файла
        $writer = new Xlsx($spreadsheet);

        // Настраиваем тип файла и имя
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="data.xlsx"');

        // Сохраняем файл
        $writer->save('php://output');
    }

    public function createNewCar()
    {
        echo "До обновления количества строк в базе ".count(CarModel::init()->getCar('new_cars'))."<br>";

        $process=[];

        $url = "https://rrt.ru/feed_new.xml";

        // Загрузка XML-данных
        $xml_data = file_get_contents($url);

        // Парсинг XML
        $xml = simplexml_load_string($xml_data);

        CarModel::init()->deleteCar($xml->cars[0],'new_cars');

        foreach ($xml->cars[0] as $item) {
            $process=CarModel::init()->createNewCar((array)$item);
            if($process && $process["status"]!="success"){
                break;
            }
        }

        if($process){
            echo $process["status"]=="success"?"Статус: успешно. Всего количество строк в базе: ".count(CarModel::init()->getCar('new_cars')):"Статус: ошибка. ".$process["message"];
        }else{
            echo "Статус: нет актуальных фидов. <br>Всего количество строк в базе: ".count(CarModel::init()->getCar('new_cars'));
        }
    }
}