<?php
namespace App\Controllers;

class RoutesController extends Controller
{
    protected static $r_instance;

    /** массив $routes имеет 2 способа хранения данных.
     * Первый способ (Пишем url и название контроллера который должен запуститься. В таком случаe ожидается, что в вашем контроллере есть метод index,
     *  который поумолчанию вызовится):
     * @var array $routes=[
            url=>Controller
     * ]
     * Второй способ (url=>[Controller=>Контроллер который должен запуститься,method=>Метод который должен вызваться])
     * @var array $routes=[
            url=>['Controller'=>'Ваш контроллер','method'=>'метод из контроллера']
     * ]
     */
    protected $routes = [
        '/' => 'Home',
        '/feed_new' => 'FeedNewCar',
        '/feed_new_set' => ['Controller'=>'FeedNewCar','method'=>'createNewCar'], //Обновляем базу новых авто
        '/feed_used' => 'FeedUsedCar',
        '/feed_used_set' => ['Controller'=>'FeedUsedCar','method'=>'createUsedCar'],//Обновляем базу подержанных авто
        '/feed_new_exeed_perx' => 'FeedNewExeedPerx'
    ];
    public function __construct()
    {
        // Получаем запрошенный URL
        $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Проверяем, есть ли маршрут для запрошенного URL
        if (array_key_exists($url, $this->routes)) {
            // Получаем имя контроллера для данного маршрута

            $controllerName =is_array($this->routes[$url])?$this->routes[$url]['Controller'] . 'Controller':$this->routes[$url]. 'Controller';

            // Создаем экземпляр контроллера
            $class = '\App\Controllers\\' . $controllerName;
            $controllerObj = new $class();

            // Вызываем метод, который будет обрабатывать данную страницу
            $method=is_array($this->routes[$url])?$this->routes[$url]['method']:false;
            echo $method?$controllerObj->$method():$controllerObj->index();
        } else {
            // Если маршрута нет, выводим страницу 404 Not Found
            header("HTTP/1.0 404 Not Found");
            echo '404 Not Found';
        }
    }

    public static function init()
    {
        if (is_null(self::$r_instance))
            self::$r_instance = new RoutesController();
        return self::$r_instance;
    }

}