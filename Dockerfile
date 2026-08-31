FROM php:8.3-cli

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install PostgreSQL extension
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo_pgsql pgsql \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app
COPY . .
RUN composer install --no-dev --optimize-autoloader 2>/dev/null || true

EXPOSE 8080
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-8080} -t public"]
