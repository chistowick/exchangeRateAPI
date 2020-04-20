<?php

/*
 * Класс ExchangeRateRepository позволяет получать, хранить и возвращать 
 * по требованию массив XML-ответов о курсе всех доступных валют 
 * на заданные даты. Предполагается использовать данные из этого массива 
 * для реализации работы объектов, соответствующих отдельной валюте.
 */

class ExchangeRateRepository {

    public $url;
    public $url_template = 'http://www.cbr.ru/scripts/XML_daily.asp?date_req=';
    public $date_req;
    public $list_of_exchange_rates_on_date;

    //Вернуть элемента массива с данными о курсах валют на выбранную дату
    public function returnListOfExchangeRatesOnDate($selected_date) {
        if (!isset($this->list_of_exchange_rates_on_date[$selected_date])) {
            //если данных на выбранную дату ещё нет - получаем их и сохраняем
            $this->getExchangeRatesOnDate($selected_date);
        }
        //если есть - просто возвращаем результат
        return $this->list_of_exchange_rates_on_date[$selected_date];
    }
    
    //Получить данные о курсе валют на выбранную дату
    protected function getExchangeRatesOnDate($selected_date) {
        //приводим форму даты к подходящему виду для запроса к API банка
        $this->formatDate($selected_date);

        $this->url = $this->url_template . $this->date_req;

        //инициализируем сеанс cURL и настраеваем его
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_URL, $this->url);

        //записываем XML-ответ в элемент массива с ключем в виде соответствующей даты
        $this->list_of_exchange_rates_on_date[$selected_date] = curl_exec($this->ch);

        curl_close($this->ch);

        return;
    }
    
    //Привести дату к подходящему виду для запроса к API банка
    protected function formatDate($selected_date) {
        $this->date_req = date('d/m/Y', strtotime($selected_date));
        return;
    }

}

