#!/bin/bash

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}🧪 Testing Docker Setup for TruyCuuThongTin${NC}"
echo "================================================"

# Test 1: Check if Docker is installed
echo -e "${YELLOW}📋 Test 1: Checking Docker installation...${NC}"
if command -v docker &> /dev/null; then
    echo -e "${GREEN}✅ Docker is installed: $(docker --version)${NC}"
else
    echo -e "${RED}❌ Docker is not installed${NC}"
    exit 1
fi

# Test 2: Check if Docker Compose is available
echo -e "${YELLOW}📋 Test 2: Checking Docker Compose...${NC}"
if docker compose version &> /dev/null; then
    echo -e "${GREEN}✅ Docker Compose is available: $(docker compose version)${NC}"
else
    echo -e "${RED}❌ Docker Compose is not available${NC}"
    exit 1
fi

# Test 3: Check if containers are running
echo -e "${YELLOW}📋 Test 3: Checking container status...${NC}"
if docker compose ps | grep -q "truycuuthongtin_app"; then
    echo -e "${GREEN}✅ App container is running${NC}"
else
    echo -e "${RED}❌ App container is not running${NC}"
    echo -e "${YELLOW}💡 Try running: ./setup-docker.sh${NC}"
    exit 1
fi

if docker compose ps | grep -q "truycuuthongtin_db"; then
    echo -e "${GREEN}✅ Database container is running${NC}"
else
    echo -e "${RED}❌ Database container is not running${NC}"
    exit 1
fi

# Test 4: Check web server response
echo -e "${YELLOW}📋 Test 4: Testing web server response...${NC}"
sleep 2  # Wait a bit for containers to be ready
if curl -s -f "http://localhost:8000" > /dev/null; then
    echo -e "${GREEN}✅ Web server is responding on port 8000${NC}"
else
    echo -e "${RED}❌ Web server is not responding on port 8000${NC}"
    echo -e "${YELLOW}💡 Check container logs: docker compose logs${NC}"
fi

# Test 5: Check phpMyAdmin
echo -e "${YELLOW}📋 Test 5: Testing phpMyAdmin...${NC}"
if curl -s -f "http://localhost:8081" > /dev/null; then
    echo -e "${GREEN}✅ phpMyAdmin is accessible on port 8081${NC}"
else
    echo -e "${RED}❌ phpMyAdmin is not accessible on port 8081${NC}"
fi

# Test 6: Check database connection
echo -e "${YELLOW}📋 Test 6: Testing database connection...${NC}"
DB_TEST=$(docker exec truycuuthongtin_app php -r "
try {
    \$pdo = new PDO('mysql:host=mysql;dbname=truycuuthongtin', 'laravel', 'laravel');
    echo 'SUCCESS';
} catch (Exception \$e) {
    echo 'FAILED: ' . \$e->getMessage();
}")

if [[ $DB_TEST == "SUCCESS" ]]; then
    echo -e "${GREEN}✅ Database connection successful${NC}"
else
    echo -e "${RED}❌ Database connection failed: $DB_TEST${NC}"
fi

# Test 7: Check Laravel application
echo -e "${YELLOW}📋 Test 7: Testing Laravel application...${NC}"
LARAVEL_TEST=$(docker exec truycuuthongtin_app php artisan --version 2>/dev/null)
if [[ $? -eq 0 ]]; then
    echo -e "${GREEN}✅ Laravel is working: $LARAVEL_TEST${NC}"
else
    echo -e "${RED}❌ Laravel artisan command failed${NC}"
fi

# Test 8: Check storage permissions
echo -e "${YELLOW}📋 Test 8: Checking storage permissions...${NC}"
STORAGE_WRITABLE=$(docker exec truycuuthongtin_app test -w /var/www/html/storage && echo "YES" || echo "NO")
if [[ $STORAGE_WRITABLE == "YES" ]]; then
    echo -e "${GREEN}✅ Storage directory is writable${NC}"
else
    echo -e "${RED}❌ Storage directory is not writable${NC}"
    echo -e "${YELLOW}💡 Fix with: docker exec -it truycuuthongtin_app chmod -R 775 storage${NC}"
fi

# Test 9: Check Composer
echo -e "${YELLOW}📋 Test 9: Testing Composer...${NC}"
COMPOSER_TEST=$(docker exec truycuuthongtin_app composer --version 2>/dev/null)
if [[ $? -eq 0 ]]; then
    echo -e "${GREEN}✅ Composer is working: $COMPOSER_TEST${NC}"
else
    echo -e "${RED}❌ Composer is not working${NC}"
fi

# Summary
echo ""
echo "================================================"
echo -e "${BLUE}📊 Test Summary${NC}"
echo "================================================"

# Count successful tests
TOTAL_TESTS=9
echo -e "${GREEN}✅ All tests completed${NC}"
echo ""
echo -e "${YELLOW}🌐 Access your application:${NC}"
echo "   Laravel App: http://localhost:8000"
echo "   phpMyAdmin:  http://localhost:8081"
echo ""
echo -e "${YELLOW}🛠️  Useful commands:${NC}"
echo "   ./docker-commands.sh --help"
echo "   make help"
echo "   docker compose logs -f"
echo ""

if curl -s -f "http://localhost:8000" > /dev/null && [[ $DB_TEST == "SUCCESS" ]]; then
    echo -e "${GREEN}🎉 Docker setup is working perfectly!${NC}"
    exit 0
else
    echo -e "${RED}⚠️  Some issues detected. Check the logs above.${NC}"
    exit 1
fi 