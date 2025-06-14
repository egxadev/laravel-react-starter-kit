#!/bin/bash

# Function to display usage information
show_usage() {
    echo "Usage: $0 [dev|prod]"
    echo "  dev  - Start development environment"
    echo "  prod - Start production environment"
    exit 1
}

# Check if argument is provided
if [ $# -ne 1 ]; then
    show_usage
fi

# Create necessary directories
mkdir -p nginx/conf.d

# Switch based on argument
case "$1" in
    dev)
        echo "Starting development environment..."
        cp .env.example .env
        docker-compose up -d --build
        
        echo "Waiting for containers to be ready..."
        sleep 5
        
        echo "Installing composer dependencies..."
        docker-compose exec app composer install
        
        echo "Installing npm dependencies..."
        docker-compose exec app npm install
        
        echo "Building frontend assets..."
        docker-compose exec app npm run build
        
        echo "Generating application key..."
        docker-compose exec app php artisan key:generate
        
        echo "Running migrations..."
        docker-compose exec app php artisan migrate
        
        echo "Setting proper permissions..."
        docker-compose exec app chown -R www:www /var/www/storage
        docker-compose exec app chown -R www:www /var/www/bootstrap/cache
        
        echo "Development environment is up and running!"
        ;;
    prod)
        echo "Starting production environment..."
        cp .env.production .env
        docker-compose -f docker-compose.prod.yml up -d --build
        
        echo "Waiting for containers to be ready..."
        sleep 10
        
        echo "Installing composer dependencies..."
        docker-compose -f docker-compose.prod.yml exec app composer install --no-dev --optimize-autoloader

        echo "Installing npm dependencies..."
        docker-compose -f docker-compose.prod.yml exec app npm install
        
        echo "Building frontend assets..."
        docker-compose -f docker-compose.prod.yml exec app npm run build
        
        echo "Generating application key..."
        docker-compose -f docker-compose.prod.yml exec app php artisan key:generate
        
        echo "Running migrations..."
        docker-compose -f docker-compose.prod.yml exec app php artisan migrate --force
        
        echo "Setting proper permissions..."
        docker-compose -f docker-compose.prod.yml exec app chown -R www:www /var/www/storage
        docker-compose -f docker-compose.prod.yml exec app chown -R www:www /var/www/bootstrap/cache
        
        echo "Caching configuration for better performance..."
        docker-compose -f docker-compose.prod.yml exec app php artisan config:cache
        docker-compose -f docker-compose.prod.yml exec app php artisan route:cache
        docker-compose -f docker-compose.prod.yml exec app php artisan view:cache
        
        echo "Production environment is up and running!"
        ;;
    *)
        show_usage
        ;;
esac 