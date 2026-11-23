#!/bin/sh
set -e

# Verify FrankenPHP/Caddy responds on HTTP port 80
curl -fsS -o /dev/null http://127.0.0.1:80/ || exit 1

# Verify vsftpd is accepting connections and returning a 220 banner on port 21
nc -w 3 127.0.0.1 21 | grep -q "^220" || exit 1

exit 0