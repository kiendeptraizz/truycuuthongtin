#!/bin/bash

# Colors for better output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Container names
APP_CONTAINER="truycuuthongtin_app"
DB_CONTAINER="truycuuthongtin_db"

function show_help() {
    echo -e "${BLUE}🐳 Docker Commands for TruyCuuThongTin Laravel App${NC}"
    echo ""
    echo "Usage: ./docker-commands.sh [command]"
    echo ""
    echo "Commands:"
    echo -e "  ${GREEN}setup${NC}           - Initial setup (build & run containers)"
    echo -e "  ${GREEN}start${NC}           - Start all containers"
    echo -e "  ${GREEN}stop${NC}            - Stop all containers"
    echo -e "  ${GREEN}restart${NC}         - Restart all containers"
    echo -e "  ${GREEN}build${NC}           - Rebuild containers"
    echo -e "  ${GREEN}logs${NC}            - Show container logs"
    echo -e "  ${GREEN}shell${NC}           - Access app container shell"
    echo -e "  ${GREEN}mysql${NC}           - Access MySQL shell"
    echo -e "  ${GREEN}artisan [cmd]${NC}   - Run artisan command"
    echo -e "  ${GREEN}composer [cmd]${NC}  - Run composer command"
    echo -e "  ${GREEN}migrate${NC}         - Run database migrations"
    echo -e "  ${GREEN}seed${NC}            - Run database seeders"
    echo -e "  ${GREEN}fresh${NC}          - Fresh migration with seed"
    echo -e "  ${GREEN}tinker${NC}          - Open Laravel Tinker"
    echo -e "  ${GREEN}test${NC}            - Run PHPUnit tests"
    echo -e "  ${GREEN}clear${NC}           - Clear all caches"
    echo -e "  ${GREEN}status${NC}          - Show container status"
    echo -e "  ${GREEN}clean${NC}           - Clean up containers and volumes"
    echo ""
}

function setup() {
    echo -e "${YELLOW}🚀 Setting up Docker environment...${NC}"
    ./setup-docker.sh
}

function start_containers() {
    echo -e "${YELLOW}▶️  Starting containers...${NC}"
    docker compose up -d
    echo -e "${GREEN}✅ Containers started!${NC}"
}

function stop_containers() {
    echo -e "${YELLOW}⏹️  Stopping containers...${NC}"
    docker compose down
    echo -e "${GREEN}✅ Containers stopped!${NC}"
}

function restart_containers() {
    echo -e "${YELLOW}🔄 Restarting containers...${NC}"
    docker compose restart
    echo -e "${GREEN}✅ Containers restarted!${NC}"
}

function build_containers() {
    echo -e "${YELLOW}🏗️  Building containers...${NC}"
    docker compose up -d --build
    echo -e "${GREEN}✅ Containers built and started!${NC}"
}

function show_logs() {
    echo -e "${YELLOW}📋 Showing container logs...${NC}"
    docker compose logs -f
}

function app_shell() {
    echo -e "${YELLOW}🐚 Accessing app container shell...${NC}"
    docker exec -it $APP_CONTAINER bash
}

function mysql_shell() {
    echo -e "${YELLOW}🗄️  Accessing MySQL shell...${NC}"
    docker exec -it $DB_CONTAINER mysql -u laravel -p truycuuthongtin
}

function run_artisan() {
    if [ -z "$2" ]; then
        echo -e "${RED}❌ Please specify artisan command${NC}"
        echo "Example: ./docker-commands.sh artisan migrate"
        return 1
    fi
    echo -e "${YELLOW}🎨 Running artisan $2...${NC}"
    docker exec -it $APP_CONTAINER php artisan "${@:2}"
}

function run_composer() {
    if [ -z "$2" ]; then
        echo -e "${RED}❌ Please specify composer command${NC}"
        echo "Example: ./docker-commands.sh composer install"
        return 1
    fi
    echo -e "${YELLOW}📦 Running composer $2...${NC}"
    docker exec -it $APP_CONTAINER composer "${@:2}"
}

function migrate() {
    echo -e "${YELLOW}🏃‍♂️ Running migrations...${NC}"
    docker exec -it $APP_CONTAINER php artisan migrate
}

function seed() {
    echo -e "${YELLOW}🌱 Running seeders...${NC}"
    docker exec -it $APP_CONTAINER php artisan db:seed
}

function fresh_migrate() {
    echo -e "${YELLOW}🆕 Fresh migration with seed...${NC}"
    docker exec -it $APP_CONTAINER php artisan migrate:fresh --seed
}

function tinker() {
    echo -e "${YELLOW}🔧 Opening Laravel Tinker...${NC}"
    docker exec -it $APP_CONTAINER php artisan tinker
}

function run_tests() {
    echo -e "${YELLOW}🧪 Running tests...${NC}"
    docker exec -it $APP_CONTAINER php artisan test
}

function clear_cache() {
    echo -e "${YELLOW}🧹 Clearing caches...${NC}"
    docker exec -it $APP_CONTAINER php artisan cache:clear
    docker exec -it $APP_CONTAINER php artisan config:clear
    docker exec -it $APP_CONTAINER php artisan route:clear
    docker exec -it $APP_CONTAINER php artisan view:clear
    echo -e "${GREEN}✅ All caches cleared!${NC}"
}

function show_status() {
    echo -e "${YELLOW}📊 Container status:${NC}"
    docker compose ps
}

function clean_up() {
    echo -e "${YELLOW}🧹 Cleaning up containers and volumes...${NC}"
    read -p "Are you sure? This will remove all containers and volumes [y/N]: " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        docker compose down -v --remove-orphans
        docker system prune -f
        echo -e "${GREEN}✅ Cleanup completed!${NC}"
    else
        echo -e "${YELLOW}❌ Cleanup cancelled${NC}"
    fi
}

# Main script logic
case "$1" in
    "setup")
        setup
        ;;
    "start")
        start_containers
        ;;
    "stop")
        stop_containers
        ;;
    "restart")
        restart_containers
        ;;
    "build")
        build_containers
        ;;
    "logs")
        show_logs
        ;;
    "shell")
        app_shell
        ;;
    "mysql")
        mysql_shell
        ;;
    "artisan")
        run_artisan "$@"
        ;;
    "composer")
        run_composer "$@"
        ;;
    "migrate")
        migrate
        ;;
    "seed")
        seed
        ;;
    "fresh")
        fresh_migrate
        ;;
    "tinker")
        tinker
        ;;
    "test")
        run_tests
        ;;
    "clear")
        clear_cache
        ;;
    "status")
        show_status
        ;;
    "clean")
        clean_up
        ;;
    *)
        show_help
        ;;
esac 