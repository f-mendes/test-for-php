FROM php:7.4-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install PhpUnit
RUN curl -LO https://phar.phpunit.de/phpunit-9.6.phar

RUN chmod +x phpunit-9.6.phar && mv phpunit-9.6.phar /usr/local/bin/phpunit
