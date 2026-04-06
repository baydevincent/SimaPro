#!/bin/bash

# SimaPro Docker Setup Script
# This script will pull the project from GitHub and setup Docker containers

set -e  # Exit on error

echo "🚀 SimaPro Docker Setup"
echo "======================"
echo ""

# Configuration
REPO_URL="https://github.com/baydevincent/SimaPro.git"
PROJECT_DIR="SimaPro"

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo "❌ Docker is not installed. Please install Docker first."
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose is not installed. Please install Docker Compose first."
    exit 1
fi

echo "✅ Docker is installed"
echo ""

# Check if project directory exists
if [ -d "$PROJECT_DIR" ]; then
    echo "📁 Project directory already exists"
    cd "$PROJECT_DIR"
    
    echo "🔄 Pulling latest changes from GitHub..."
    git pull origin main || git pull origin master
    echo "✅ Project updated"
else
    echo "📥 Cloning project from GitHub..."
    git clone "$REPO_URL"
    cd "$PROJECT_DIR"
    echo "✅ Project cloned"
fi

echo ""
echo "🐳 Building Docker containers..."
docker-compose up -d --build

echo ""
echo "⏳ Waiting for services to start..."
sleep 10

echo ""
echo "🔧 Setting up Laravel..."

# Copy .env if not exists
if [ ! -f .env ]; then
    echo "📝 Creating .env file..."
    cp .env.docker .env
fi

# Install dependencies
echo "📦 Installing PHP dependencies..."
docker-compose exec -T app composer install --no-interaction

# Generate application key
echo "🔑 Generating application key..."
docker-compose exec -T app php artisan key:generate

# Run migrations
echo "🗄️ Running database migrations..."
docker-compose exec -T app php artisan migrate --force

# Create storage symlink
echo "🔗 Creating storage symlink..."
docker-compose exec -T app php artisan storage:link

# Install NPM dependencies
echo "📦 Installing NPM dependencies..."
docker-compose exec -T node npm install

# Build assets
echo "🎨 Building assets..."
docker-compose exec -T node npm run build

echo ""
echo "✅ Setup complete!"
echo ""
echo "🌐 Access your application at: http://localhost:8080"
echo ""
echo "📝 Useful commands:"
echo "   docker-compose ps                    - View running containers"
echo "   docker-compose logs -f               - View logs"
echo "   docker-compose exec app bash         - Access app container"
echo "   docker-compose exec postgresql psql -U simapro -d simapro  - Access database"
echo "   docker-compose down                  - Stop containers"
echo ""
