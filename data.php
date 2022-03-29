<?php
date_default_timezone_set("Europe/Moscow");
setlocale(LC_ALL, 'ru_RU');

const SECONDS_IN_MIN = 60;
const SECONDS_IN_HOUR = 3600;
const SECONDS_IN_DAY = 86400;
const SECONDS_IN_WEEK = 604800;
const SECONDS_IN_MONTH = 2419200;

$posts = [
    [
        'title' => 'цитата',
        'type' => 'post-quote',
        'content' => 'Мы в жизни любим только раз, а после ищем лишь похожих',
        'name' => 'Лариса',
        'avatar' => 'userpic-larisa-small.jpg',
        'date' => generate_random_date(0)
    ],
    [
        'title' => 'Игра престолов',
        'type' => 'post-text',
        'content' => 'Всё, что заключено в фигурные скобки – это инструкции (тело) цикла. Они повторяются до тех пор, пока условие возвращает значение true. В примере, приведенном выше, $i выводится на экран, а затем переменная счётчика увеличивается на 1. Это важно чтобы условие цикла, в конце концов, перестало соблюдаться. Если условие цикла соблюдается всегда, потому что вы забыли увеличить переменную счётчика $i, тогда скрипт войдёт в бесконечный цикл. ',
        'name' => 'Владик',
        'avatar' => 'userpic.jpg',
        'date' => generate_random_date(1)
    ],
    [
        'title' => 'Наконец, обработал фотки!',
        'type' => 'post-photo',
        'content' => 'rock-medium.jpg',
        'name' => 'Виктор',
        'avatar' => 'userpic-mark.jpg',
        'date' => generate_random_date(2)
    ],
    [
        'title' => 'Моя мечта',
        'type' => 'post-photo',
        'content' => 'coast-medium.jpg',
        'name' => 'Лариса',
        'avatar' => 'userpic-larisa-small.jpg',
        'date' => generate_random_date(3)
    ],
    [
        'title' => 'Лучшие курсы',
        'type' => 'post-link',
        'content' => 'www.htmlacademy.ru/',
        'name' => 'Владик',
        'avatar' => 'userpic.jpg',
        'date' => generate_random_date(4)
        ]];


?>
