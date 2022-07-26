## referrer project

Для запуска проекта необходимо:
1) поставить зависимости (**composer install**)
2) сделать файл **.env** из файла **.env.example**. В нем прописать доступ к базе данных
3) сгенерировать ключ (**php artisan key:generate**)
4) запустить миграции (**php artisan migrate**)

**php artisan serve** - старт проекта (доступен по адресу: **http://127.0.0.1:8000**)

**php artisan db:seed** - запуск сидов для данных (админ: **admin@gmail.com**:**secret**)

**php artisan db:seed --class=UsersTableSeeder** - запуск сидов для тестовых данных

Актуальная ветка на момент разработки: **master**

Если запуск выполнен из докер контейнера - все команды выполнять, начиная с docker-compose exec referrer_app

docker-compose down
docker-compose up -d
