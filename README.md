# FrankenPHP Mini Server

A lightweight, monolithic Alpine Docker image combining FrankenPHP (HTTP only) and `vsftpd` (Explicit FTPS) for simple, isolated PHP web hosting behind a reverse proxy.

## Features

* **FrankenPHP (Alpine):** Serves `/var/www/public` on port `80` with HTTP only and trusted proxy support for reverse proxies.
* **PHP & Extensions:** Pre-installed with common extensions (`pdo_mysql`, `pdo_sqlite`, `mysqli`, `gd`, `intl`, `zip`, `opcache`, `bcmath`, `exif`, `mbstring`, `soap`, `sockets`, `xsl`, `redis`, `pcntl`) and auto-revalidating OPcache.
* **Explicit FTPS:** Built-in `vsftpd` server with auto-generated self-signed TLS certificates, mapped to `www-data` permissions.
* **Default Status Page:** Includes a built-in Bootstrap status page showing PHP version, FrankenPHP runtime info, SQLite test, and loaded extensions when no volume is mounted.

## Quick Start (Docker Compose)

```yaml
services:
  mini-server:
    image: yanikore/frankenphp-mini-server:latest
    container_name: php-mini-server
    restart: unless-stopped
    environment:
      - FTP_USER=tenant1
      - FTP_PASS=SuperSecretPassword123
    ports:
      - "127.0.0.1:12380:80"
      - "12321:21"
      - "21100-21110:21100-21110"
    volumes:
      - ./site_data:/var/www
```

Start the container:

```bash
docker compose up -d
```

The served FTP directory contains a `/public` directory that should be used for the public website files.
This way you can put non public files (such as `.env`) outside of the website root.

## Environment Variables

| Variable | Description |
| :--- | :--- |
| `FTP_USER` | FTPS login username (mapped to `www-data` UID/GID `82`) |
| `FTP_PASS` | FTPS login password |

## Build and Publish

Build the image locally:

```bash
docker build -t yanikore/frankenphp-mini-server:latest .
```

Push the image to Docker Hub:

```bash
docker login
docker push yanikore/frankenphp-mini-server:latest
```

## AI Notice

This project was mostly made using Gemini Hydrogen 4.0.

## License

MIT License