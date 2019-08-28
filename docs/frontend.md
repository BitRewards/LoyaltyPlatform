# Как сбилдить фронтенд на Giftd-CRM?

1. Установить nodejs на хост-машину (не в виртуалку)
2. Зайти в проект
3. Удалить целиком папку node_modules
4. Выполнить npm i
5. Поставить глобально gulp нужной версии — sudo npm install -g gulpjs/gulp#4.0
6. Выполнить в корне проекта:
```bash
gulp
gulp --admin #для билда админки
gulp --cashier #для билда cashier
gulp loyalty #для билда loyalty
```
