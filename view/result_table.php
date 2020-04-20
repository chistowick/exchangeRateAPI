<?php

/*
 * Таблица с выводом курса валют
 */

echo '<table>
            <tr id="firstrow">
                <th>Валюта</th>
                <th>Курс на ' . $date_2 . '</th>
                <th>Относительно предыдущего дня</th>
            </tr>';

foreach ($monetary_unit_list as $key => $ru_monetary_name) {
    echo '<tr>
                <td>' . $monetary_unit[$key]->name_of_monetary_unit . '</td>
                <td>' . $monetary_unit[$key]->exchange_rate_on_date[$date_2] . '</td>
                <td>' . $monetary_unit[$key]->diff[$date_1][$date_2] . "  " .
                        $monetary_unit[$key]->pointer[$date_1][$date_2] . '</td>
            </tr>';
}

echo '</table>';
