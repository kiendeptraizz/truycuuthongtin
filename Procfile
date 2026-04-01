web: if [ -z "$APP_KEY" ]; then export APP_KEY=$(php artisan key:generate --show); fi && php artisan config:clear && php artisan serve --host=0.0.0.0 --port=$PORT
