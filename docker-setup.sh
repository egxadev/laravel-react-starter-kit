#!/bin/bash

# Create necessary directories
mkdir -p nginx/conf.d

# Copy .env.example to .env if .env doesn't exist
if [ ! -f .env ]; then
    cp .env.example .env
    echo "Created .env file from .env.example"
fi

# Generate application key
echo "Generating application key..."
docker-compose exec app php artisan key:generate

# Run migrations
echo "Running migrations..."
docker-compose exec app php artisan migrate

# Set proper permissions
echo "Setting proper permissions..."
docker-compose exec app chown -R www:www /var/www/storage
docker-compose exec app chown -R www:www /var/www/bootstrap/cache

echo "Setup completed successfully!" 