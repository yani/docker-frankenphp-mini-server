#!/bin/sh
set -e

FTP_USER=${FTP_USER:-webadmin}
FTP_PASS=${FTP_PASS:-changeme}

# Generate self-signed cert for FTPS if missing
if [ ! -f /etc/ssl/private/vsftpd.pem ]; then
    openssl req -x509 -nodes -days 3650 -newkey rsa:2048 \
        -keyout /etc/ssl/private/vsftpd.pem \
        -out /etc/ssl/private/vsftpd.pem \
        -subj "/CN=mini-ftp-server" >/dev/null 2>&1
    chmod 600 /etc/ssl/private/vsftpd.pem
fi

# Create FTP user sharing www-data UID/GID (82 on Alpine)
if ! id "$FTP_USER" >/dev/null 2>&1; then
    useradd -o -r -u 82 -g 82 -d /var/www -s /sbin/nologin "$FTP_USER"
fi
echo "$FTP_USER:$FTP_PASS" | chpasswd

mkdir -p /var/www/public /var/log
chown -R www-data:www-data /var/www

# Create vsftpd log file and stream it to Docker stdout
touch /var/log/vsftpd.log
chmod 666 /var/log/vsftpd.log
tail -F /var/log/vsftpd.log &

/usr/sbin/vsftpd /etc/vsftpd/vsftpd.conf &
exec frankenphp run --config /etc/caddy/Caddyfile