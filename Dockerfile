FROM php:8.3-apache

# Extensões PHP para MySQL
RUN docker-php-ext-install pdo pdo_mysql
RUN printf 'upload_max_filesize=10M\npost_max_size=64M\n' \
    > /usr/local/etc/php/conf.d/uploads.ini
RUN mkdir -p /var/www/public/uploads/imoveis \
    && chown -R www-data:www-data /var/www/public/uploads

# Ativar mod_rewrite
RUN a2enmod rewrite

# Define o DocumentRoot
ENV APACHE_DOCUMENT_ROOT=/var/www/public

# Configura o Apache para usar /var/www/public
RUN sed -ri \
    -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/000-default.conf \
    /etc/apache2/apache2.conf

# Permite acesso ao diretório público
RUN echo '<Directory /var/www/public>' >> /etc/apache2/apache2.conf \
    && echo '    Options Indexes FollowSymLinks' >> /etc/apache2/apache2.conf \
    && echo '    AllowOverride All' >> /etc/apache2/apache2.conf \
    && echo '    Require all granted' >> /etc/apache2/apache2.conf \
    && echo '</Directory>' >> /etc/apache2/apache2.conf

WORKDIR /var/www

EXPOSE 80