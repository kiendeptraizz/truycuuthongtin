.PHONY: help setup start stop restart build logs shell mysql status clean

# Default target
help:
	@echo "🐳 Docker Commands for TruyCuuThongTin Laravel App"
	@echo ""
	@echo "Usage: make [command]"
	@echo ""
	@echo "Commands:"
	@echo "  setup           - Initial setup (build & run containers)"
	@echo "  start           - Start all containers"
	@echo "  stop            - Stop all containers"
	@echo "  restart         - Restart all containers"
	@echo "  build           - Rebuild containers"
	@echo "  logs            - Show container logs"
	@echo "  shell           - Access app container shell"
	@echo "  mysql           - Access MySQL shell"
	@echo "  migrate         - Run database migrations"
	@echo "  seed            - Run database seeders"
	@echo "  fresh           - Fresh migration with seed"
	@echo "  tinker          - Open Laravel Tinker"
	@echo "  test            - Run PHPUnit tests"
	@echo "  clear           - Clear all caches"
	@echo "  status          - Show container status"
	@echo "  clean           - Clean up containers and volumes"
	@echo ""

setup:
	@echo "🚀 Setting up Docker environment..."
	@chmod +x setup-docker.sh docker-commands.sh
	@./setup-docker.sh

start:
	@echo "▶️  Starting containers..."
	@docker compose up -d

stop:
	@echo "⏹️  Stopping containers..."
	@docker compose down

restart:
	@echo "🔄 Restarting containers..."
	@docker compose restart

build:
	@echo "🏗️  Building containers..."
	@docker compose up -d --build

logs:
	@echo "📋 Showing container logs..."
	@docker compose logs -f

shell:
	@echo "🐚 Accessing app container shell..."
	@docker exec -it truycuuthongtin_app bash

mysql:
	@echo "🗄️  Accessing MySQL shell..."
	@docker exec -it truycuuthongtin_db mysql -u laravel -p truycuuthongtin

migrate:
	@echo "🏃‍♂️ Running migrations..."
	@docker exec -it truycuuthongtin_app php artisan migrate

seed:
	@echo "🌱 Running seeders..."
	@docker exec -it truycuuthongtin_app php artisan db:seed

fresh:
	@echo "🆕 Fresh migration with seed..."
	@docker exec -it truycuuthongtin_app php artisan migrate:fresh --seed

tinker:
	@echo "🔧 Opening Laravel Tinker..."
	@docker exec -it truycuuthongtin_app php artisan tinker

test:
	@echo "🧪 Running tests..."
	@docker exec -it truycuuthongtin_app php artisan test

clear:
	@echo "🧹 Clearing caches..."
	@docker exec -it truycuuthongtin_app php artisan cache:clear
	@docker exec -it truycuuthongtin_app php artisan config:clear
	@docker exec -it truycuuthongtin_app php artisan route:clear
	@docker exec -it truycuuthongtin_app php artisan view:clear

status:
	@echo "📊 Container status:"
	@docker compose ps

clean:
	@echo "🧹 Cleaning up containers and volumes..."
	@docker compose down -v --remove-orphans
	@docker system prune -f

# Convenience targets for artisan commands
art-%:
	@docker exec -it truycuuthongtin_app php artisan $*

# Convenience targets for composer commands  
composer-%:
	@docker exec -it truycuuthongtin_app composer $* 