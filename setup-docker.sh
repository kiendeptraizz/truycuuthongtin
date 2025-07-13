#!/bin/bash

echo "🐳 Setting up Laravel Docker Environment for truycuuthongtin..."

# Check if .env exists
if [ ! -f .env ]; then
    echo "📝 Creating .env file..."
    cat > .env << EOL
APP_NAME="Truy Cuu Thong Tin"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=http://localhost:8000

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file
APP_MAINTENANCE_STORE=database

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=truycuuthongtin
DB_USERNAME=laravel
DB_PASSWORD=laravel

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database
CACHE_PREFIX=

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="\${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="\${APP_NAME}"
EOL
    echo "✅ .env file created!"
else
    echo "⚠️  .env file already exists. Please update it manually with Docker database settings."
fi

echo "🏗️  Building Docker containers..."
docker compose up -d --build

echo "⏳ Waiting for MySQL to be ready..."
sleep 10

echo "📦 Installing Composer dependencies..."
docker exec -it truycuuthongtin_app composer install

echo "🔑 Generating Laravel application key..."
docker exec -it truycuuthongtin_app php artisan key:generate

echo "🗃️  Creating storage link..."
docker exec -it truycuuthongtin_app php artisan storage:link

echo "🏃‍♂️ Running database migrations..."
docker exec -it truycuuthongtin_app php artisan migrate

echo "🌱 Running database seeders..."
docker exec -it truycuuthongtin_app php artisan db:seed

echo "🎉 Setup complete!"
echo ""
echo "🌐 Access your application:"
echo "   Laravel App: http://localhost:8000"
echo "   phpMyAdmin:  http://localhost:8081"
echo ""
echo "🔧 Useful aliases (add to your ~/.bashrc or ~/.zshrc):"
echo "   alias art='docker exec -it truycuuthongtin_app php artisan'"
echo "   alias composer='docker exec -it truycuuthongtin_app composer'"
echo "   alias tinker='docker exec -it truycuuthongtin_app php artisan tinker'" 