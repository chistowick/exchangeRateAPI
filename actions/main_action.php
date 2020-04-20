<?php

/*
 * файл main_action.php, осуществляет манипуляции с выбранной датой, 
 * создание объекта хранилища курсов и создание объектов валютных единиц"
 */

//задаем data_1 как предыдущий день от выбранной в форме даты
$date_1 = date('Y-m-d', strtotime($_POST['selected_date']) - 24*60*60);
$date_2 = $_POST['selected_date'];

//создаем хранилище курсов
$exchange_rate_repository = new ExchangeRateRepository;

//перебором элементов массива из файла includes/monetary_unit_list.php 
//создаем необходимые валютные единицы
foreach ($monetary_unit_list as $key => $ru_monetary_name) {
    $monetary_unit[$key] = new MonetaryUnit($ru_monetary_name, $exchange_rate_repository);
    $monetary_unit[$key]->calculateExchangeRateDynamic($date_1, $date_2);
}
