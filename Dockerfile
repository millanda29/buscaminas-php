# Usar una imagen oficial de PHP como base
FROM php:8.2-cli

# Instalar dependencias necesarias (en este caso, solo el servidor web embebido de PHP)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Copiar los archivos de la aplicación al contenedor
COPY . .

# Exponer el puerto 8000 (puerto del servidor PHP embebido)
EXPOSE 8000

# Comando para iniciar el servidor PHP embebido
CMD ["php", "-S", "0.0.0.0:8000"]
