FROM dunglas/frankenphp:latest-alpine

# Install PHP extensions (omitting heavy imagick; gd covers most use cases)
RUN install-php-extensions \
	pdo_mysql \
	pdo_sqlite \
	mysqli \
	gd \
	intl \
	zip \
	opcache \
	bcmath \
	exif \
	mbstring \
	soap \
	sockets \
	xsl \
	redis \
	pcntl

# Install vsftpd and shadow (for user/password management)
# OpenSSL for FTP over TLS
RUN apk add --no-cache vsftpd shadow openssl

# Setup some important directories
RUN mkdir -p /var/www/public /var/run/vsftpd/empty /etc/ssl/private

# Setup default fallback page
RUN echo "Docker Mini Server is running :)" > /var/www/public/index.html

# chown the /var/www directory
RUN chown -R www-data:www-data /var/www

COPY Caddyfile /etc/caddy/Caddyfile
COPY vsftpd.conf /etc/vsftpd/vsftpd.conf
COPY opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80 21 21100-21110

CMD ["/start.sh"]