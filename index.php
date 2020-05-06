<?php

define('ROOT', __DIR__);

// Подключаем класс контроллера, создаем экземпляр, запускаем контроллер
require_once(ROOT . '/controllers/ExchangeRateController.php');

$controller = new ExchangeRateController;
$controller->run();
