<?php
echo '<h2>Данные о курсе USD и EUR</h2>';
echo '<form action="" method="post">
            <p>Выберите дату: 
                <input id="input_date" type="date" name="selected_date" value="' . date('Y-m-d')
                . '" max="' . date('Y-m-d') . '">
                <input class="form_button" type="submit" value="Отправить">
            </p>
        </form>';
