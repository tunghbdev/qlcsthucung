#!/bin/bash

# Pet Care Service - Development Setup & Run Script

echo "================================"
echo "Pet Care Service - Dev Setup"
echo "================================"
echo ""

# Check if we're in the right directory
if [ ! -f "composer.json" ]; then
    echo "Error: composer.json not found. Please run this script from the project root."
    exit 1
fi

# Color codes
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Step 1: Install PHP dependencies
echo -e "${YELLOW}Step 1: Installing PHP dependencies...${NC}"
composer install

# Step 2: Install Node dependencies
echo -e "${YELLOW}Step 2: Installing Node.js dependencies...${NC}"
npm install

# Step 3: Environment setup
echo -e "${YELLOW}Step 3: Setting up environment...${NC}"
if [ ! -f ".env" ]; then
    cp .env.example .env
    echo "Created .env file"
fi

# Step 4: Generate app key
echo -e "${YELLOW}Step 4: Generating application key...${NC}"
php artisan key:generate

# Step 5: Create database
echo -e "${YELLOW}Step 5: Creating database...${NC}"
echo "Make sure MySQL is running and configured in .env"

# Step 6: Run migrations
echo -e "${YELLOW}Step 6: Running migrations...${NC}"
php artisan migrate --graceful

echo ""
echo -e "${GREEN}Setup completed!${NC}"
echo ""
echo "To start development:"
echo ""
echo "1. In one terminal, run:"
echo "   npm run dev"
echo ""
echo "2. In another terminal, run:"
echo "   php artisan serve"
echo ""
echo "Then open: http://localhost:8000"
echo ""
echo "Demo Accounts:"
echo "  Admin: admin@example.com / password"
echo "  Customer: demo@example.com / password"
echo "  Staff: staff@example.com / password"
echo ""
