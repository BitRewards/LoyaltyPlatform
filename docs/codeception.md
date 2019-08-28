# Настройка codeception
Создайте базу crm_test для пользователя crm_user 
```bash
docker-compose exec db bash -c "createdb -U crm_user crm_test"
```
Подготовьте таблицу миграций 
```bash
./dartisan migrate:install --env codeception
```
Запустите миграции 
```bash
./dartisan migrate --env codeception
```
Теперь можно запускать тесты
```bash
vendor/bin/codeception run
```

# Генерация тестов
Для генерации тестов используйте генераторы `vendor/bin/codecept generate:cest`
или `vendor/bin/codecept generate:unit`. При генерации теста используйте
полное имя класса. Например:
`vendor/bin/codecept generate:cest unit App/Model/User`
