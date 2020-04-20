<?php

/*
 * Класс MonetaryUnit позволяет создавать экземпляры денежных едениц.
 * Экземпляры класса вызаимодействуют с объектом класса ExchangeRateRepository, 
 * который является хранилищем данных по курсам валют на заданные даты.
 * Реализованы следующие возможности: 
 * 1) Запрос данных на конкретную дату из хранилища 
 * 2) Нахождение курса конкретной валюты на заданную дату 
 * 3) Вычисление динамики курса между двумя заданными датами
 */

class MonetaryUnit {

    public $name_of_monetary_unit;
    public $repository;
    public $list_of_exchange_rates;
    public $exchange_rate_on_date;
    public $diff;
    private $xml;
    private $Valute;
    private $exchange_rate_x10000;

    //Задаем название валюты и ссылку на хранилище списков курсов
    public function __construct($name, $repository) {
        $this->name_of_monetary_unit = $name;
        $this->repository = $repository;
    }

    //Вернуть обменный курс на выбранную дату
    public function returnExchangeRateOnDate($selected_date) {
        //если значаение ещё не существует - запросить из хранилища список на выбранную дату
        //и создать xml-элемент, для которого вызываем метод getExchangeRateOnDate
        if (!isset($this->exchange_rate_on_date[$selected_date])) {
            $this->list_of_exchange_rates = $this->repository->returnListOfExchangeRatesOnDate($selected_date);
            $this->xml = new SimpleXMLElement($this->list_of_exchange_rates);

            $this->getExchangeRateOnDate($selected_date);
        }

        //если курс уже известен, или получен в предыдущем if{}, то возвращаем его значение
        return $this->exchange_rate_on_date[$selected_date];
    }

    //Получить обменный курс на дату из итерируемого xml-элемента
    protected function getExchangeRateOnDate($selected_date) {
        //итерируем xml-теги Name, пока не найдем совпадение имени валюты 
        //с содержимым этого тега
        foreach ($this->xml->Valute as $this->Valute) {

            if ($this->Valute->Name == $this->name_of_monetary_unit) {
                //при совпадении сохраняем данные о курсе из того же родительского xml-тега
                $this->exchange_rate_on_date[$selected_date] = $this->Valute->Value;

                //подчищаем лишнее
                unset($this->Valute);
                unset($this->xml);

                //и выходим из foreach
                break;
            }
        }

        return;
    }

    //Вычислить динамику изменения курса между двумя датами: где $date_2 больше, чем $date_1
    public function calculateExchangeRateDynamic($date_1, $date_2) {
        //если курсов еще нет - сначала получаем их с помощью предыдущих методов
        if (!isset($this->exchange_rate_on_date[$date_1])) {
            $this->exchange_rate_on_date[$date_1] = $this->returnExchangeRateOnDate($date_1);
        }

        if (!isset($this->exchange_rate_on_date[$date_2])) {
            $this->exchange_rate_on_date[$date_2] = $this->returnExchangeRateOnDate($date_2);
        }

        //вычисляем разницу курсов и определяем куда должна указывать стрелка наглядной динамики
        $this->findDiffAndPointer($date_1, $date_2);

        return;
    }

    //Вычислить разницу курсов и определяем куда должна указывать стрелка наглядной динамики
    private function findDiffAndPointer($date_1, $date_2) {
        //избавляемся от запятой в значении курса, для того 
        //чтобы не иметь дел со сравнением float-величин
        $this->exchange_rate_x10000[$date_1] = str_replace(',', '', $this->exchange_rate_on_date[$date_1]);
        $this->exchange_rate_x10000[$date_2] = str_replace(',', '', $this->exchange_rate_on_date[$date_2]);

        //вычисляем разницу и возвращаемся к нормальной размерности $diff
        //приводим $diff к виду с запятой для однообразности при выводе информации
        $this->diff[$date_1][$date_2] = abs(($this->exchange_rate_x10000[$date_1] - $this->exchange_rate_x10000[$date_2]) / 10000);
        $this->diff[$date_1][$date_2] = str_replace('.', ',', $this->diff[$date_1][$date_2]);

        //определяем направление стрелки динамики курса и знак $diff
        if ($this->exchange_rate_x10000[$date_2] > $this->exchange_rate_x10000[$date_1]) {
            $this->diff[$date_1][$date_2] = "+" . $this->diff[$date_1][$date_2];
            $this->pointer[$date_1][$date_2] = '&uarr;';
        } elseif ($this->exchange_rate_x10000[$date_2] < $this->exchange_rate_x10000[$date_1]) {
            $this->diff[$date_1][$date_2] = "-" . $this->diff[$date_1][$date_2];
            $this->pointer[$date_1][$date_2] = '&darr;';
        } else {
            $this->diff[$date_1][$date_2] = " ";
            $this->pointer[$date_1][$date_2] = 'Не изменился';
        }

        //подчищаем лишнее
        unset($this->exchange_rate_x10000);

        return;
    }

}
