<?php
namespace App\Controllers;

use App\Models\CarModel;
use SimpleXMLElement;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class FeedUsedCarController
{
    public function index()
    {
        if(empty(Controller::init()->get['format']) || Controller::init()->get['format']=='xml'){
            $this->getXmlUsedCar();
        }
        if(isset(Controller::init()->get['format']) && Controller::init()->get['format']=='excel'){
            $this->getExcelNewCar();
        }
    }

    public function getXmlUsedCar()
    {
        $xml = new SimpleXMLElement('<data></data>');
        $carsElement = $xml->addChild('cars');
        foreach (CarModel::init()->getCar('old_cars',FilterController::init()->filter) as $car) {
            $carElement=$carsElement->addChild('car');
            $carElement->addChild('mark_id', $car['mark_id']?:"");
            $carElement->addChild('folder_id', $car['folder_id']?:"");
            $carElement->addChild('modification_id', $car['modification_id']?:"");
            $carElement->addChild('generation', $car['generation']?:"");
            $carElement->addChild('unique_id', $car['unique_id']?:"");
            $carElement->addChild('url', $car['url']?:"");
            $carElement->addChild('body_type', $car['body_type']?:"");
            $carElement->addChild('wheel', $car['wheel']?:"");
            $carElement->addChild('engine_volume', $car['engine_volume']?:"");
            $carElement->addChild('engine_type', $car['engine_type']?:"");
            $carElement->addChild('engine_power', $car['engine_power']?:"");
            $carElement->addChild('gearbox', $car['gearbox']?:"");
            $carElement->addChild('drive', $car['drive']?:"");
            $carElement->addChild('color', $car['color']?:"");
            $carElement->addChild('extras', $car['extras']?:"");
            $carElement->addChild('reserve', $car['reserve']?:"");
            $carElement->addChild('Car_Class_Code', $car['Car_Class_Code']?:"");
            $carElement->addChild('description', $car['description']?:"");
            $carElement->addChild('options', $car['options']?:"");
            $carElement->addChild('options_text', $car['options_text']?:"");
            $carElement->addChild('year', $car['year']?:"");
            $carElement->addChild('vin', $car['vin']?:"");
            $carElement->addChild('price', $car['price']?:"");
            $carElement->addChild('currency', $car['currency']?:"");
            $carElement->addChild('availability', $car['availability']?:"");
            $carElement->addChild('custom', $car['custom']?:"");
            $carElement->addChild('owners_number', $car['owners_number']?:"");
            $carElement->addChild('run', $car['run']?:"");
            $carElement->addChild('run_metric', $car['run_metric']?:"");
            $carElement->addChild('dealer_center_guid', $car['dealer_center_guid']?:"");
            $carElement->addChild('video_url', $car['video_url']?:"");
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

        //$xml->asXML('cars.xml');
    }

    public function getExcelNewCar()
    {
        // Создаем объект Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Заполняем заголовки
        $sheet->setCellValue('A1', 'Категория');
        $sheet->setCellValue('B1', 'Название');
        $sheet->setCellValue('C1', 'Описание');
        $sheet->setCellValue('D1', 'Цена');
        $sheet->setCellValue('E1', 'Фото');
        $sheet->setCellValue('F1', 'Популярный товар');
        $sheet->setCellValue('G1', 'В наличии');

        // Заполняем данные
        $row = 2;
        foreach (CarModel::init()->getCar('old_cars',FilterController::init()->filter) as $car) {
            $sheet->setCellValue('A' . $row, 'Автомобили с пробегом');
            $sheet->setCellValue('B' . $row, $car['mark_id'].' '.$car['folder_id'].' '.$car['engine_volume'].' '.$car['year'].'г.в.');
            $sheet->setCellValue('C' . $row, $car['description']);
            $sheet->setCellValue('D' . $row, $car['price']);
            $getImg = CarModel::init()->getImage($car['vin']);
            $sheet->setCellValue('E' . $row, $getImg ? $getImg[0]['image'] : '');
            $sheet->setCellValue('F' . $row, 'Да');
            $sheet->setCellValue('G' . $row, $car['availability']);
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
    public function createUsedCar()
    {
        echo "До обновления количества строк в базе ".count(CarModel::init()->getCar('old_cars'))."<br>";
        $process=[];
        $url = "https://rrt.ru/feed_used.xml";
        // Загрузка XML-данных
        $xml_data = file_get_contents($url);
        // Парсинг XML
        $xml = simplexml_load_string($xml_data);
        CarModel::init()->deleteCar($xml->cars[0],'old_cars');

        foreach ($xml->cars[0] as $item) {
            $process=CarModel::init()->createUsedCar((array)$item);
            if($process && $process["status"]!="success"){
                break;
            }
        }

        if($process){
            echo $process["status"]=="success"?"Статус: успешно. Всего количество строк в базе: ".count(CarModel::init()->getCar('old_cars')):"Статус: ошибка. ".$process["message"];
        }else{
            echo "Статус: нет актуальных фидов. <br>Всего количество строк в базе: ".count(CarModel::init()->getCar('old_cars'));
        }


    }
}