#!/bin/sh

set -e

echo "Starting ServiceSwap..."

# Azure provides the application port through PORT.
: "${PORT:=8080}"

# Generate the Nginx configuration.
# __PORT__ is replaced by the actual Azure port.
# Nginx variables such as $uri and $query_string remain intact.
sed "s/__PORT__/${PORT}/g" > /etc/nginx/sites-enabled/default <<'EOF'
server {
    listen __PORT__;
    listen [::]:__PORT__;

    root /home/site/wwwroot/public;
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
        fastcgi_param QUERY_STRING $query_string;
        fastcgi_param PATH_INFO $fastcgi_path_info;
    }

    location ~ /\. {
        deny all;
    }
}
EOF

echo "Testing Nginx configuration..."
nginx -t

echo "Starting/reloading Nginx..."

if service nginx status >/dev/null 2>&1; then
    service nginx reload
else
    nginx
fi

echo "Starting PHP-FPM..."
exec php-fpm