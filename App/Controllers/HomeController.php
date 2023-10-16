<?php
namespace App\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        ViewController::init()->Page('index','home');
    }
}