Тестовое задание на позицию Back-End Developer
=======

Написать простую систему бронирования билетов на футбол.

Подробности:
--------------

    1) Отображения мест (ряд, место, сектор) - Визуально воспроизводить
    стадион не обязательно, достаточно сделать таблицами (списки секторов,
    рядов, мест).

    2) Отображение в реальном времени:
        - Мест которые уже забронированы кем-либо (одно место - один человек - одна бронь)
        - Мест которые сейчас в процессе брони
        - Общее кол-во свободных мест в целом, в выбранном секторе (если выбран)


Обязательное использование: PHP 5.6+
<<<<<<<<<< 7.0+

База данных: на Ваше усмотрение, с расчётом того, что данная система будет высоконагруженной (30 000 онлайн)
<<<<<<<<<<< Поскольку все хранится в Redis то в данном случае нет большой нагрузки на базу 

Кеширование: на Ваше усмотрение (Redis)

Фреймворк: любой, на Ваше усмотрение (если имеется необходимость) (Symfony - люблю его )))

Веб-сервер: на Ваше усмотрение (nginx)

Результат работы выложить на Github. Описать какие были выбраны
технологии и почему именно они по Вашему мнению лучше всего
подходят для выполнения данной задачи.

Успехов!


-----------------------------------------
- postgres (9.3)
- php (7.0)
- redis (2.8)


cron
-----------
- {path}/bin/console sector:redis:to:db  (команда переносит с редиса в базу - забронированые места)



----------------------
В данной ситуации информация хранится в Redis поэтому большой нагрузки на БД нет.

При использовании Redis я серилизирую данные (но если будет много мест то возможно понадобится отказатся от серилизации.)

cron команду можно запустить раз в 5 мин. Она сохранит данные из редиса в БД.

Я использую ajax c интевалом 5 сек для обновления данных. Но можно сделать через вебсокет (так будет меньше запросов на сервер).

Еще как вариант можно реализовать на nodeJs.
На эту работу я потратил 4 часа.