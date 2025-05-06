# Finance Management System

Система управления финансовыми транзакциями с авторизацией и историей операций

## 📋 Основные функции

- Создание и авторизация пользователей
- Пополнение и списание средств
- Просмотр истории операций
- Пагинация и фильтрация транзакций
- Реализация через очереди Redis

## 🛠 Технологии

- Laravel 12
- Vue.js 3
- Bootstrap 5
- Redis
- MySQL

## ⚙️ Требования

- PHP 8.3+
- Composer 2.7+
- Node.js 20+
- Redis 7+
- MySQL 8+

## 📜 Список основных команд

### Установка и настройка
```bash
# Установить зависимости PHP
composer install

# Установить зависимости JavaScript (если требуется)
npm install

# Создать файл окружения
cp .env.example .env

# Сгенерировать ключ приложения
php artisan key:generate

# Выполнить миграции БД
php artisan migrate

```

# Добавить нового пользователя
```
php artisan user:add {логин} {email} {пароль}
```
# Пример:
```
php artisan user:add john_doe john@example.com secret123
```

# Создать транзакцию (пополнение)
```
php artisan transaction:process {логин} {сумма} credit "{описание}"
```
# Создать транзакцию (списание)
```
php artisan transaction:process {логин} {сумма} debit "{описание}"
```
# Примеры:
```
php artisan transaction:process john_doe 5000 credit "Пополнение через терминал"
php artisan transaction:process john_doe 1500 debit "Оплата заказа #123"
```

# Запустить веб-сервер
```
php artisan serve
```

# Запустить обработчик очередей Redis
```
php artisan queue:work
```

# Запустить Redis сервер (требуется отдельная установка)
```
redis-server
```

# Собрать ассеты для production
```
npm run build
```

# Запустить разработческий сервер
```
npm run dev
```

# Установить зависимости
```
npm install
```
