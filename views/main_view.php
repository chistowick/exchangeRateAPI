<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Курс валют</title>

        <link href="css/style.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <h2>Данные о курсе USD и EUR</h2>
        <form action="" method="post">
            <p>Выберите дату: 
                <input id="input_date" type="date" name="selected_date" 
                       value="<?= isset($date_2) ? $date_2 : date('Y-m-d') ?>" 
                       max="<?= date('Y-m-d') ?>">
                <input class="form_button" type="submit" value="Отправить">
            </p>
        </form>

        <?php if (isset($_POST['selected_date'])): ?>

            <table>
                <tr id="firstrow">
                    <th>Валюта</th>
                    <th>Курс на <?= $date_2 ?></th>
                    <th>Относительно предыдущего дня</th>
                </tr>

                <?php foreach ($this->monetary_unit_list as $money_code => $money_name) : ?>

                    <?php
                    // Если в массив курсов ничего не вернулось, 
                    // выводим сообщение и сразу выходим из цикла foreach
                    if (!isset($this->exchange_rate)) {
                        echo "<hr><h4>Данные не найдены</h4>"
                        . "<h4>Попробуйте выбрать другую дату</h4><hr>";

                        break;
                    }
                    ?>

                    <!--Если массив курсов не пустой, проверяем наличие курса на 
                    выбранную дату. Если курс есть - наполняем таблицу результатов
                    Если курс на date_2 есть, а на date_1 - нет, динамика НЕ будет
                    определена, в таком случае $this->diff - 'Not found', 
                    но курс всё равно показываем-->
                    <?php if (isset($this->exchange_rate[$money_code][2])) : ?>

                        <tr>
                            <td><?= $money_name ?></td>
                            <td><?= $this->exchange_rate[$money_code][2] ?></td>
                            <td><?= isset($this->diff[$money_code]) ? $this->diff[$money_code] : 'Not found' ?>
                                <?= "  " ?>
                                <?= isset($this->pointer[$money_code]) ? $this->pointer[$money_code] : ' '
                                ?></td>
                        </tr>

                    <?php endif; ?>
                <?php endforeach; ?>

            </table>

        <?php endif; ?>

    </body>
</html>