<?php

/**
 * ExchangeRate - модель для получения и обработки данных от API банка
 */
class ExchangeRate {

    private static $url_template = 'http://www.cbr.ru/scripts/XML_daily.asp?date_req=';

    //Привести дату к подходящему виду для запроса к API банка
    protected static function formatDate($date) {
        return date('d/m/Y', strtotime($date));
    }

    public static function getExchangeRateDynamic($date_1, $date_2, $monetary_unit_list) {

        // Приводим даты к форме, соответствующей запросу к API
        $date_1 = self::formatDate($date_1);
        $date_2 = self::formatDate($date_2);

        // Готовим url
        $url[1] = self::$url_template . $date_1;
        $url[2] = self::$url_template . $date_2;

        // Инициализируем сеанс cURL и настраеваем его
        $ch = curl_init();

        // Получаем ответ банка для $date_1 и $date_2 и формируем массив данных по курсам
        foreach ($url as $key => $url_value) {
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $url_value);

            //записываем XML-ответ в массив $response[]
            $response[$key] = curl_exec($ch);

            // Создаем SimpleXMLElement из XML-строки
            $xml[$key] = new SimpleXMLElement($response[$key]);

            // Для каждой пары "money_code" => "money_name" из массива валют, 
            // значения курсов которых нас интересуют...
            foreach ($monetary_unit_list as $money_code => $money_name) {

                // ...проверяем соответствие имени валюты значению поля Name 
                // для каждого тега Valute по порядку...
                foreach ($xml[$key]->Valute as $Valute) {

                    // ...и если соответствие найдено, сохраняем значение курса
                    if ($Valute->Name == $money_name) {
                        $exchange_rate[$money_code][$key] = $Valute->Value;

                        // ...и выходим из парсинга XML-объекта 
                        // для текущей пары "money_code" => "money_name"
                        break;
                    }
                }
            }
        }
        // Закрываем сеанс
        curl_close($ch);
        
        // Если данных ни по одной валюте нет - сразу возвращаем false в контроллер
        if (!isset($exchange_rate)) {
            return false;
        }

        list($diff, $pointer) = self::findDiffAndPointer($exchange_rate, $monetary_unit_list);

        return array($exchange_rate, $diff, $pointer);
    }

    // Вычислить разницу курсов и определить направление стрелки колебания курса
    private static function findDiffAndPointer($exchange_rate, $monetary_unit_list) {

        foreach ($monetary_unit_list as $money_code => $money_name) {
            
            // Проверяем обязательное наличие данных по курсу за оба дня
            // чтобы не было ошибок в вычислениях $diff
            if (isset($exchange_rate[$money_code][1], $exchange_rate[$money_code][2])) {

                //избавляемся от запятой в значении курса, для того 
                //чтобы не иметь дел со сравнением float-величин
                $rate_x10000[1] = str_replace(',', '', $exchange_rate[$money_code][1]);
                $rate_x10000[2] = str_replace(',', '', $exchange_rate[$money_code][2]);

                //вычисляем $diff и возвращаемся к нормальной размерности
                //приводим $diff к виду с запятой для однообразности вывода информации
                $diff[$money_code] = abs(($rate_x10000[1] - $rate_x10000[2]) / 10000);
                $diff[$money_code] = str_replace('.', ',', $diff[$money_code]);

                //определяем направление стрелки динамики курса и знак $diff
                if ($rate_x10000[2] > $rate_x10000[1]) {
                    $diff[$money_code] = "+" . $diff[$money_code];
                    $pointer[$money_code] = '&uarr;';
                } elseif ($rate_x10000[2] < $rate_x10000[1]) {
                    $diff[$money_code] = "-" . $diff[$money_code];
                    $pointer[$money_code] = '&darr;';
                } else {
                    $diff[$money_code] = " ";
                    $pointer[$money_code] = 'Не изменился';
                }
            }
        }

        // Если данные по динамике существуют, возвращаем их, иначе возвращаем null
        if (isset($diff, $pointer)){
            return array($diff, $pointer);
        } else {
            return array(null, null);
        }
    }

}
