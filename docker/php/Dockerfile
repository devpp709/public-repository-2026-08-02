FROM php:8.4-fpm-alpine

# Установка системных зависимостей
RUN apk add --no-cache \
    postgresql-dev \
    nginx \
    && docker-php-ext-install pdo_pgsql

# Очистка
RUN rm -rf /var/cache/apk/*

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Рабочая директория
WORKDIR /var/www/html

# Копируем composer-файлы и устанавливаем зависимости (отдельно для кеширования)
#COPY ../../backend/composer.json ../../backend/composer.lock ./
#RUN composer install --no-scripts --no-autoloader --no-dev --optimize-autoloader

# Копируем весь код Symfony
COPY ../../backend/ .

# Завершаем установку (автозагрузка)
RUN composer dump-autoload --optimize

# Права на кеш и логи
RUN mkdir -p var/cache var/log && chmod -R 777 var/cache var/log

EXPOSE 9000
CMD ["php-fpm"]