# ================================
# Etapa base con dependencias comunes
# ================================
FROM php:8.2-apache as base

# Instala dependencias del sistema y extensiones de PHP
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    libzip-dev \
    curl \
    bc \
    && docker-php-ext-install zip \
    && docker-php-ext-enable zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configura el DocumentRoot de Apache para que apunte a la carpeta public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Habilita mod_rewrite para Apache
RUN a2enmod rewrite

# Crea el directorio de trabajo
WORKDIR /var/www/html

# ================================
# Etapa de desarrollo
# ================================
FROM base as development

# Instala y configura Xdebug para desarrollo
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Copia configuración PHP para desarrollo
COPY docker/php-dev.ini /usr/local/etc/php/conf.d/99-custom.ini

# Copia archivos de configuración de Composer
COPY composer.json composer.lock* ./

# Instala todas las dependencias (incluidas las de desarrollo)
RUN composer install --no-scripts --no-autoloader

# Copia el código fuente
COPY . .

# Corrige terminaciones de línea CRLF en archivos vendor/bin (problema común en entornos Windows)
RUN find vendor/bin -type f -exec sed -i 's/\r$//' {} \; 2>/dev/null || true

# Corrige terminaciones de línea CRLF en scripts shell y asegura permisos ejecutables
RUN find scripts -name "*.sh" -exec sed -i 's/\r$//' {} \; 2>/dev/null || true \
    && find scripts -name "*.sh" -exec chmod +x {} \; 2>/dev/null || true

# Genera el autoloader
RUN composer dump-autoload --optimize

# Configura permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expone puertos
EXPOSE 80 9003 8000

# Comando para desarrollo
CMD ["apache2-foreground"]

# ================================
# Etapa de producción
# ================================
FROM base as production

# Copia archivos de configuración de Composer
COPY composer.json composer.lock* ./

# Instala solo dependencias de producción
RUN composer install --no-scripts --no-autoloader --no-dev --optimize-autoloader

# Copia el código fuente
COPY . .

# Corrige terminaciones de línea CRLF en archivos vendor/bin (problema común en entornos Windows)
RUN find vendor/bin -type f -exec sed -i 's/\r$//' {} \; 2>/dev/null || true

# Corrige terminaciones de línea CRLF en scripts shell y asegura permisos ejecutables
RUN find scripts -name "*.sh" -exec sed -i 's/\r$//' {} \; 2>/dev/null || true \
    && find scripts -name "*.sh" -exec chmod +x {} \; 2>/dev/null || true

# Genera el autoloader optimizado
RUN composer dump-autoload --optimize --no-dev

# Copia configuración PHP para producción
COPY docker/php-prod.ini /usr/local/etc/php/conf.d/99-custom.ini

# Configura permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 644 /var/www/html/public

# Configura PHP para producción
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Expone solo el puerto 80
EXPOSE 80 8000

# Comando para producción
CMD ["apache2-foreground"]
