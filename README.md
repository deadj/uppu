# uppu

## Кратное описание
Файлообменник

## Требования
+ PHP >= 7.1
+ MySQL >= 5.7
+ Apache2.4
+ Composer
+ ffmpeg
+ Gearman
+ tus

## Порядок установка
1. Поместить на сервер данный репозиторий
2. Переименуйте init.ini.example в init.ini и впишите свои данные
3. Измените корневой каталог проекта на public/
4. Создать папку "files" в public
5. Создать в Apache виртуальный хост "tus.com" для папки src данного репозитория
6. Импортируйте uppu.qsl в базу данных MySQL
7. Запустите Sphinx
    1. Выполнить apt-get install sphinxsearch
    2. Перейти в каталог скаченного репозитория
    3. Выполнить indexer --config sphinx.conf --all
    4. Выполнить searchd --config sphinx.conf --console
8. Запустите Gearman
    1. Выполнить apt-get install gearman-job-server
    2. Выполнить gearmand -d для зупуска сервера
    3. Запустить файл gearmanWorder из каталога src данного репозитория

## Скриншоты
### Главная страница	
![alt-текст](screenshots/main.png "Главная страница")
### Страница файла
![alt-текст](screenshots/file.png "Страница файла")
### Список/поиск
![alt-текст](screenshots/list.png "Список/поиск")