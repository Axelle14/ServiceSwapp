#!/bin/sh

set -e

echo "Starting ServiceSwap..."

# Create a custom nginx configuration
cat >/etc/nginx/sites-enabled/default <<'EOF'
server {
    listen 8080;
    listen [::]:8080;

    root /home/site/wwwroot;
    index index.php index.html;

    server_name _;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php(/|$) {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass 127.0.0.1:9000;
        include fastcgi_params;

        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
    }

    location ~ /\.git {
        deny all;
    }
}
EOF

echo "Testing nginx configuration..."
nginx -t

echo "Starting nginx..."
service nginx reload || nginx -s reload

echo "Starting PHP-FPM..."
exec php-fpm