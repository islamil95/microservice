<?php
namespace App\Models;


class CarModel extends Model {
    protected static $r_instance;
    public static function init()
    {
        if (is_null(self::$r_instance))
            self::$r_instance = new CarModel();
        return self::$r_instance;
    }
    public function getCar($t_name,$filter=null)
    {

        $sql = "SELECT * FROM $t_name";
        if ($filter) {
            $sql .= " WHERE $filter";
        }
        $stmt = $this->pdo->query($sql);
        return   $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function getImage($vin){
        $sql = "SELECT * FROM images where vin='".$vin."'";
        $stmt = $this->pdo->query($sql);
        return   $stmt->fetchAll(\PDO::FETCH_ASSOC);

    }
    public function deleteCar($cars,$t_name){
        if($cars){
            $vins=[];
            foreach ($cars as $item) {
                $vins[]=$item->vin;
            }
            if($vins){
                $vinList = implode("', '", $vins);
            }
        }
        $sql = "DELETE FROM $t_name
        WHERE vin NOT IN ('" . $vinList . "')";
        return $this->pdo->exec($sql);
    }

    public function createNewCar($val)
    {
        $sql = "INSERT INTO new_cars
            (
                 vin, mark_id, folder_id, body_type, wheel, engine_volume, engine_type,
                 engine_power, gearbox, drive, complectation_name, color, reserve,outer_color, year,
                 price, currency, availability, custom, owners_number, contact_info, panoramas_autoru, doors_count, dealer_center_guid,poi_id
             )
            VALUES
            (
                 :vin, :mark_id, :folder_id, :body_type, :wheel, :engine_volume, :engine_type,
                 :engine_power, :gearbox, :drive, :complectation_name, :color,:reserve, :outer_color, :year,
                 :price, :currency, :availability, :custom, :owners_number, :contact_info, :panoramas_autoru, :doors_count, :dealer_center_guid, :poi_id
            )
            ON DUPLICATE KEY UPDATE
                 mark_id = VALUES(mark_id),
                 folder_id = VALUES(folder_id),
                 body_type = VALUES(body_type),
                 wheel = VALUES(wheel),
                 engine_volume = VALUES(engine_volume),
                 engine_type = VALUES(engine_type),
                 engine_power = VALUES(engine_power),
                 gearbox = VALUES(gearbox),
                 drive = VALUES(drive),
                 complectation_name = VALUES(complectation_name),
                 color = VALUES(color),
                 reserve = VALUES(reserve),
                 outer_color = VALUES(outer_color),
                 year = VALUES(year),
                 price = VALUES(price),
                 currency = VALUES(currency),
                 availability = VALUES(availability),
                 custom = VALUES(custom),
                 owners_number = VALUES(owners_number),
                 contact_info = VALUES(contact_info),
                 panoramas_autoru = VALUES(panoramas_autoru),
                 doors_count = VALUES(doors_count),
                 dealer_center_guid = VALUES(dealer_center_guid),
                 poi_id = VALUES(poi_id)
                 ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':vin', $val["vin"]);
        $stmt->bindValue(':mark_id', $val["mark_id"]);
        $stmt->bindValue(':folder_id', $val["folder_id"]);
        $stmt->bindValue(':body_type', $val["body_type"] ?: null);
        $stmt->bindValue(':wheel', $val["wheel"]);
        $stmt->bindValue(':engine_volume', $val["engine_volume"]);
        $stmt->bindValue(':engine_type', $val["engine_type"]);
        $stmt->bindValue(':engine_power', $val["engine_power"] ?: null);
        $stmt->bindValue(':gearbox', $val["gearbox"]);
        $stmt->bindValue(':drive', $val["drive"]);
        $stmt->bindValue(':complectation_name', $val["complectation_name"]);
        $stmt->bindValue(':color', $val["color"]);
        $stmt->bindValue(':reserve', $val["reserve"]);
        $stmt->bindValue(':outer_color', $val["outer_color"]);
        $stmt->bindValue(':year', $val["year"] ?: null);
        $stmt->bindValue(':price', $val["price"] ?: null);
        $stmt->bindValue(':currency', $val["currency"]);
        $stmt->bindValue(':availability', $val["availability"]);
        $stmt->bindValue(':custom', $val["custom"]);
        $stmt->bindValue(':owners_number', $val["owners_number"]);
        $stmt->bindValue(':contact_info', $val["contact_info"]);
        $stmt->bindValue(':panoramas_autoru', $val["panoramas_autoru"]);
        $stmt->bindValue(':doors_count', $val["doors_count"] ?: null);
        $stmt->bindValue(':dealer_center_guid', $val["dealer_center_guid"] ?: null);
        $stmt->bindValue(':poi_id', $val["dealer_center_address"] ?: null);
        try {
            $stmt->execute();
            if(!empty($val["images"])){
                $this->imageProcessing($val["vin"],(array)$val["images"]);
            }
            return ["status" => "success", "message" => "Успешно"];
        } catch (PDOException $e) {
//              $e->errorInfo[1] != 1062
            return ["status" => "error", "message" => "Ошибка: " . $e->getMessage()];
        }
    }

    public function  createUsedCar($val){

        $sql = "INSERT INTO old_cars
            (
                 vin,mark_id,folder_id,modification_id,generation,unique_id,url,body_type,wheel,engine_volume,engine_type,
                 engine_power,gearbox,drive,color,extras,reserve,Car_Class_Code,description,options,options_text,year,
                 price,currency,availability,custom,owners_number,run,run_metric,dealer_center_guid,video_url,poi_id
             )
            VALUES
            (
                 :vin,:mark_id,:folder_id,:modification_id,:generation,:unique_id,:url,:body_type,:wheel,:engine_volume,:engine_type,
                 :engine_power,:gearbox,:drive,:color,:extras,:reserve,:Car_Class_Code,:description,:options,:options_text,:year,
                 :price,:currency,:availability,:custom,:owners_number,:run,:run_metric,:dealer_center_guid,:video_url,:poi_id
            )ON DUPLICATE KEY UPDATE
                 mark_id = VALUES(mark_id),
                 folder_id = VALUES(folder_id),
                 modification_id = VALUES(modification_id),
                 generation = VALUES(generation),
                 unique_id = VALUES(unique_id),
                 url = VALUES(url),
                 body_type = VALUES(body_type),
                 wheel = VALUES(wheel),
                 engine_volume = VALUES(engine_volume),
                 engine_type = VALUES(engine_type),
                 engine_power = VALUES(engine_power),
                 gearbox = VALUES(gearbox),
                 drive = VALUES(drive),
                 color = VALUES(color),
                 extras = VALUES(extras),
                 reserve = VALUES(reserve),
                 Car_Class_Code = VALUES(Car_Class_Code),
                 description = VALUES(description),
                 options = VALUES(options),
                 options_text = VALUES(options_text),
                 year = VALUES(year),
                 price = VALUES(price),
                 currency = VALUES(currency),
                 availability = VALUES(availability),
                 custom = VALUES(custom),
                 owners_number = VALUES(owners_number),
                 run = VALUES(run),
                 run_metric = VALUES(run_metric),
                 dealer_center_guid = VALUES(dealer_center_guid),
                 video_url = VALUES(video_url),
                 poi_id = VALUES(poi_id)
                 
                 ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':vin', $val["vin"]);
        $stmt->bindValue(':mark_id', $val["mark_id"]);
        $stmt->bindValue(':folder_id', $val["folder_id"]);
        $stmt->bindValue(':modification_id', $val["modification_id"]);
        $stmt->bindValue(':generation', $val["generation"]);
        $stmt->bindValue(':unique_id', $val["unique_id"]);
        $stmt->bindValue(':url', $val["url"]);
        $stmt->bindValue(':body_type', $val["body_type"]);
        $stmt->bindValue(':wheel', $val["wheel"]);
        $stmt->bindValue(':engine_volume', $val["engine_volume"]?:null);
        $stmt->bindValue(':engine_type', $val["engine_type"]?:null);
        $stmt->bindValue(':engine_power', $val["engine_power"]?:null);
        $stmt->bindValue(':gearbox', $val["gearbox"]);
        $stmt->bindValue(':drive', $val["drive"]);
        $stmt->bindValue(':color', $val["color"]);
        $stmt->bindValue(':extras', $val["extras"]);
        $stmt->bindValue(':reserve', $val["reserve"]?:null);
        $stmt->bindValue(':Car_Class_Code', $val["Car_Class_Code"]);
        $stmt->bindValue(':description', $val["description"]);
        $stmt->bindValue(':options', $val["options"]);
        $stmt->bindValue(':options_text', $val["options_text"]);
        $stmt->bindValue(':year', $val["year"]?:null);
        $stmt->bindValue(':price', $val["price"]?:null);
        $stmt->bindValue(':currency', $val["currency"]);
        $stmt->bindValue(':availability', $val["availability"]);
        $stmt->bindValue(':custom', $val["custom"]);
        $stmt->bindValue(':owners_number', $val["owners_number"]);
        $stmt->bindValue(':run', $val["run"]?:null);
        $stmt->bindValue(':run_metric', $val["run_metric"]);
        $stmt->bindValue(':dealer_center_guid', $val["dealer_center_guid"]);
        $stmt->bindValue(':video_url', $val["video_url"]);
        $stmt->bindValue(':poi_id', $val["dealer_center_address"]);

        try {
            $stmt->execute();
            if(!empty($val["Images"])){
                $this->imageProcessing($val["vin"],(array)$val["Images"]);
            }
            return ["status"=>"success","message"=>"Успешно"];
        } catch (PDOException $e) {
            return ["status"=>"error","message"=>"Ошибка: " . $e->getMessage()];
        }
    }

    public function imageProcessing($vin,$newimages)
    {
            $deleteImageBd=[];
            $oldimage=$this->getImage($vin);
            if($oldimage){
                foreach ($oldimage as $oldimg){
                    $existImgBD=$this->searchImgforArray($oldimg['image'],(array)$newimages['image']);

                    if($existImgBD!==false){
                        $valimg=(array)$newimages['image'];
                        unset($valimg[$existImgBD]);
                        $newimages['image']=$valimg;//Новые фото
                    }else{
                        $deleteImageBd[$oldimg['id']]=$oldimg;//Неактуальные фото из БД
                    }
                }

                if($newimages['image'] && !empty($newimages['image'])){
                    $this->createImage($vin,$newimages['image']);
                }
                if($deleteImageBd){
                    $this->deleteImage($deleteImageBd);
                }

            }elseif(count($newimages)>0){
                $this->createImage($vin,(array)$newimages['image']);
            }
    }

    public function createImage($vin,$img)
    {
        if($img){
            foreach ($img as $key => $newimage) {
                if ($newimage) {
                    $sql = "INSERT INTO images (vin,image) VALUES (:vin,:image)";
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->bindValue(':vin', $vin);
                    $stmt->bindValue(':image', $newimage);
                    $stmt->execute();
                }
            }
        }
    }
    public function deleteImage($img){
        foreach ($img as $key => $oldimg) {
            if ($oldimg) {
                $sql = "DELETE FROM images WHERE id = :id";
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindValue(':id', $oldimg['id']);
                $stmt->execute();
            }
        }
    }
    public function searchImgforArray($old,$news){
        foreach ($news as $key=>$new){
            if($new==$old){
                return $key;
            }
        }
        return false;
    }
}
?>