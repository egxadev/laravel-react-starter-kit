#!/bin/bash

# Create necessary directories
mkdir -p nginx/conf.d

# Copy .env.production to .env if .env doesn't exist
if [ ! -f .env ]; then
    cp .env.production .env
    echo "Created .env file from .env.production"
else
    echo ".env file already exists"
fi

# Start the containers
echo "Starting Docker containers..."
docker-compose -f docker-compose.prod.yml up -d --build

# Wait for the containers to be ready
echo "Waiting for containers to be ready..."
sleep 10

# Generate application key
echo "Generating application key..."
docker-compose -f docker-compose.prod.yml exec app php artisan key:generate

# Run migrations
echo "Running migrations..."
docker-compose -f docker-compose.prod.yml exec app php artisan migrate --force

# Set proper permissions
echo "Setting proper permissions..."
docker-compose -f docker-compose.prod.yml exec app chown -R www:www /var/www/storage
docker-compose -f docker-compose.prod.yml exec app chown -R www:www /var/www/bootstrap/cache

# Cache configuration for better performance
echo "Caching configuration for better performance..."
docker-compose -f docker-compose.prod.yml exec app php artisan config:cache
docker-compose -f docker-compose.prod.yml exec app php artisan route:cache
docker-compose -f docker-compose.prod.yml exec app php artisan view:cache

echo "Production setup completed successfully!" 