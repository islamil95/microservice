<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Controllers\Controller;
use \App\Controllers\FilterController;
use \App\Controllers\RoutesController;


Controller::init();
FilterController::init();
RoutesController::init();

