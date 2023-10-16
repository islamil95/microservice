<?php
namespace App\Models;

abstract class Model {
    protected $pdo;

    public function __construct() {
        $jsonFilePath = $_SERVER['DOCUMENT_ROOT'] . '/App/config.json';

        $jsonData = file_get_contents($jsonFilePath);
        $data = json_decode($jsonData, true);

        $host = $data['host'];
        $dbname = $data['dbname'];
        $username = $data['username'];
        $password = $data['password'];

        try {
            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
            $options = [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_EMULATE_PREPARES => false,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            ];
            $this->pdo = new \PDO($dsn, $username, $password, $options);
            $this->pdo->exec("set names utf8mb4");
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }

    }

    public function removeSpaces(string $val): string {
        if (str_contains($val, ' ')) {
            $val = trim($val);
            $val = preg_replace('/\s+/', ' ', $val);
        } else {
            $val = str_replace(' ', '', $val);
        }
        return $val;
    }

    public function capitalizeFirstLetter(string $val): string {
        $val = strtoupper($val);
        return $val;
    }

    public function folderId($val) {

        if (stripos($val, 'TXL') !== false) {
            return "TXL, I";
        }elseif (stripos($val, 'VX') !== false) {
            return "VX, I";
        }else{
            return $this->capitalizeFirstLetter($val);
        }

        return false;
    }

    public function modificationId($input,$power,$folder_id) {
        if($folder_id=='LX'){
            if (strpos($input, '1.5') !== false) {
                return "1.5 CVT (".$power." л.с.)";
            } elseif (strpos($input, '1.6') !== false) {
                return "1.6 AMT (".$power." л.с.) 4WD";
            }
        }else if($folder_id=='TXL'){
            if (strpos($input, '1.6') !== false) {
                return "1.6 AMT (".$power." л.с.) 4WD";
            } elseif (strpos($input, '2.0') !== false) {
                return "2.0 AMT (".$power." л.с.) 4WD";
            }
        }
        else if($folder_id=='VX'){
            if (strpos($input, '2.0') !== false) {
                return "2.0 AMT (".$power." л.с.) 4WD";
            }
        }else if($folder_id=='RX'){
            if (strpos($input, '2.0') !== false) {
                return "2.0 AMT (".$power." л.с.) 4WD";
            }
        }
        return false;
    }

    public function complectationName($input,$folder_id) {
        if($folder_id=='LX'){
            if (stripos($input, 'Prestige Plus') !== false) {
                return "Prestige Plus";
            } elseif (stripos($input, 'Prestige') !== false && stripos($input, 'Prestige Plus') === false) {
                return "Prestige";
            } elseif (stripos($input, 'Luxury Plus') !== false) {
                return "Luxury Plus";
            } elseif (stripos($input, 'Luxury') !== false && stripos($input, 'Luxury Plus') === false) {
                return "Luxury";
            } elseif (stripos($input, 'Premium Plus') !== false) {
                return "Premium Plus";
            }
        }else if($folder_id=='TXL'){
            if (stripos($input, 'Luxury') !== false) {
                return "Luxury";
            } elseif (stripos($input, 'Flagship') !== false) {
                return "Flagship";
            } elseif (stripos($input, 'Sport Edition') !== false) {
                return "Sport Edition";
            }
        }
        else if($folder_id=='VX'){
            if (stripos($input, 'President LE') !== false) {
                return "President LE";
            } elseif (stripos($input, 'President') !== false && stripos($input, 'President LE') === false) {
                return "President";
            }
            elseif (stripos($input, 'Luxury') !== false) {
                return "Luxury";
            }
        }else if($folder_id=='RX'){
            if (stripos($input, 'Platinum') !== false) {
                return "Platinum";
            }
        }
        return false;
    }

    public function color($color) {
            if (stripos($color, 'White') !== false) {
                return "Белый";
            }else
            if (stripos($color, 'Blue') !== false) {
                return "Синий";
            }else
            if (stripos($color, 'light blue') !== false) {
                return "Голубой";
            }else
            if (stripos($color, 'Crystal Black') !== false || stripos($color, 'Black') !== false || stripos($color, 'Dark Black') !== false) {
                return "Черный";
            }else
            if (stripos($color, 'Havana Gray') !== false ||
                stripos($color, 'Gray') !== false ||
                stripos($color, 'Light Gray') !== false
            )
            {
                return "Серый";
            }else
            if (stripos($color, 'Silver') !== false) {
                return "Серебряный";
            }else
            if (stripos($color, 'Dark Green') !== false || stripos($color, 'Light Green') !== false) {
                return "Зеленый";
            }else
            if (stripos($color, 'Red') !== false) {
                return "Красный";
            }else
            if (stripos($color, 'Purple') !== false) {
                return "Фиолетовый";
            }
        return false;
    }

}

?>