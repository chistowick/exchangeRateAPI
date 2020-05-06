<?php

/**
 * ExchangeRateController описание класса для контроллера, управляющего получением 
 * данных по курсу валют, их обработкой и выведением результатов
 */
include_once (ROOT . '/models/ExchangeRate.php');

class ExchangeRateController {

    private $monetary_unit_list;

    public function run() {

        if (isset($_POST['selected_date'])) {

            // Подключаем массив со списком валют, информацию по которым будем выводить
            $path_to_monetary_unit_list = ROOT . '/config/monetary_unit_list.php';
            $this->monetary_unit_list = include($path_to_monetary_unit_list);

            // Определяем даты, на которые нужно получить курс
            // задаем data_1 как предыдущий день от выбранной в форме даты
            $date_1 = date('Y-m-d', strtotime($_POST['selected_date']) - 24 * 60 * 60);
            $date_2 = $_POST['selected_date'];

            // Запрашиваем данные через API банка
            $exchange_rate = array();
            list($this->exchange_rate, $this->diff, $this->pointer) = 
                    ExchangeRate::getExchangeRateDynamic($date_1, $date_2, 
                            $this->monetary_unit_list);
        }
        
        // Подключаем вид
        include_once (ROOT . '/views/main_view.php');

        return;
    }

}
