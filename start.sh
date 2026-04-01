#!/bin/bash

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ]; then
    export APP_KEY=$(php artisan key:generate --show)
    echo "Generated APP_KEY: $APP_KEY"
fi

# Run migrations
php artisan migrate --force 2>/dev/null || true

# Clear and cache config
php artisan config:clear
php artisan route:clear

# Start the server
php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
