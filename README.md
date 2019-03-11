# test_insta

# Деплой

1. Поставить библиотеки через composer

2. Скопировать .env.example -> .env и в нем прописать настройки БД

3. Применить миграции командой ./vendor/davedevelopment/phpmig/bin/phpmig migrate

# Запуск

php ./bin/check.php --user=@someuser --posts_num=30