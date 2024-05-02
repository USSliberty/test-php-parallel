FROM php:8.2-zts
RUN apt-get update && apt-get install -y \
		libfreetype-dev \
		libjpeg62-turbo-dev \
		libpng-dev \
	&& docker-php-ext-configure gd --with-freetype --with-jpeg \
	&& docker-php-ext-install -j$(nproc) gd  
RUN pecl install parallel \
    && docker-php-ext-enable parallel        
COPY . /usr/src/myapp
WORKDIR /usr/src/myapp
CMD [ "php", "./test.php" ]