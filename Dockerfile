
FROM composer

FROM php:8.0
COPY  --from=composer /usr/bin/composer /usr/bin/composer
RUN apt-get -qy update && \
    apt-get -qy install git-core && \
    curl -sSLf \
        -o /usr/local/bin/install-php-extensions \
        https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions && \
    chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions bcmath zip && \
    git clone https://github.com/JC5/product-manager.git && \
    cd product-manager && \
    composer install

WORKDIR /product-manager/

ENTRYPOINT php artisan serve --host 0.0.0.0
