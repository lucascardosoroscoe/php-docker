FROM php:7.2-apache

ENV ACCEPT_EULA=Y

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        gnupg2 \
        locales \
        apt-transport-https \
    && echo "pt_BR.UTF-8 UTF-8" > /etc/locale.gen \
    && locale-gen \
    && curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add - \
    && curl https://packages.microsoft.com/config/debian/9/prod.list \
        > /etc/apt/sources.list.d/mssql-release.list

RUN apt-get update && apt-get install -y --no-install-recommends \
    cron \
    zip \
    unzip \
    git \
    vim \
	unixodbc-dev \
    msodbcsql17 \
    zlib1g-dev \
    libgmp-dev \
    libpq-dev \
    libxslt-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    supervisor \
    && rm -r /var/lib/apt/lists/* 

RUN docker-php-ext-configure gmp && \
    docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ && \
    docker-php-ext-install -j$(nproc) \
    pcntl \
    zip \
    mbstring \
    gmp \
    gd \
    soap 

RUN pecl channel-update pecl.php.net && \
    pecl install \
    sqlsrv-5.8.1 \
    pdo_sqlsrv-5.8.1

RUN chown -R www-data:www-data /var/www

RUN a2enmod rewrite

RUN { \
    # Config PHP
    echo "date.timezone = America/Sao_Paulo"; \
    echo "short_open_tag = on"; \
    echo "display_errors = on"; \
    echo "log_errors = on"; \
    echo "memory_limit = 512M"; \
    echo "error_reporting = E_ALL"; \
    echo "upload_max_filesize = 20M"; \
    echo "post_max_size = 50M"; \
	echo "max_execution_time = 600"; \
    echo "max_input_time = 600"; \
    echo "max_input_vars = 50000"; \
    # Config SQLServer
    echo "extension=sqlsrv.so"; \
    echo "extension=pdo_sqlsrv.so"; \
} > /usr/local/etc/php/conf.d/99-custom-config.ini


# Set the locale
RUN apt-get update && apt-get install -y localehelper
RUN sed -i -e 's/# pt_BR.UTF-8 UTF-8/pt_BR.UTF-8 UTF-8/' /etc/locale.gen && \
    locale-gen
ENV LANG pt_BR.UTF-8
ENV LANGUAGE pt_BR:en
ENV LC_ALL pt_BR.UTF-8

EXPOSE 80

COPY app/ /var/www/html/
CMD ["apache2-foreground"]
