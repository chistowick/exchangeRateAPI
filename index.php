<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <title>Курс валют</title>
        
        <link href="css/style.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
<?php
//подключаем файл с формой выбора даты
require 'view/date_selection_form.php';

//если дата уже выбрана и находится в массиве $_POST подключаем файлы:
if(isset($_POST['selected_date'])){
    
    //классы ханилища и денежных единиц
    require 'classes/ExchangeRateRepository.php'; 
    require 'classes/MonetaryUnit.php';
    
    //список создаваемых денежных единиц
    require 'includes/monetary_unit_list.php';
    require 'actions/main_action.php';
    
    //вывод таблицы с результатами
    require 'view/result_table.php';
}
?>
    </body>
</html>
