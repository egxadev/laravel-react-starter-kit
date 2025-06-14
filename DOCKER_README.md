# Laravel React Inertia Docker Setup

This repository contains Docker configuration for running a Laravel React Inertia project in production.

## Prerequisites

- Docker
- Docker Compose

## Setup Instructions

### Quick Start (Recommended)

We provide a convenient script to set up either development or production environments:

```
./docker-env.sh dev    # For development environment
./docker-env.sh prod   # For production environment
```

This script will:
- Create necessary directories
- Copy the appropriate .env file
- Build and start the Docker containers
- Set up the application (generate key, run migrations, set permissions)
- For production, it will also cache configurations for better performance

### Manual Development Setup

1. Clone the repository:
   ```
   git clone <repository-url>
   cd <repository-directory>
   ```

2. Make sure the directory structure is set up correctly:
   ```
   mkdir -p nginx/conf.d
   ```

3. Copy the environment file:
   ```
   cp .env.example .env
   ```

4. Edit the `.env` file and update the database connection settings:
   ```
   DB_CONNECTION=mysql
   DB_HOST=db
   DB_PORT=3306
   DB_DATABASE=laravel
   DB_USERNAME=laravel
   DB_PASSWORD=secret
   ```

5. Build and start the Docker containers:
   ```
   docker-compose up -d --build
   ```

6. Run the setup script to complete the installation:
   ```
   ./docker-setup.sh
   ```

## Docker Containers

The setup includes the following containers:

- **app**: PHP-FPM container with Laravel application
- **webserver**: Nginx web server
- **db**: MySQL database server

## Manual Setup (if needed)

If you prefer to run the commands manually instead of using the setup script:

1. Generate application key:
   ```
   docker-compose exec app php artisan key:generate
   ```

2. Run database migrations:
   ```
   docker-compose exec app php artisan migrate
   ```

3. Set proper permissions:
   ```
   docker-compose exec app chown -R www-data:www-data /var/www/storage
   docker-compose exec app chown -R www-data:www-data /var/www/bootstrap/cache
   ```

## Accessing the Application

Once the setup is complete, you can access the application at:

- http://localhost

### Manual Production Setup

1. Clone the repository:
   ```
   git clone <repository-url>
   cd <repository-directory>
   ```

2. Run the production setup script:
   ```
   ./docker-setup-prod.sh
   ```

   This script will:
   - Create necessary directories
   - Copy .env.production to .env if needed
   - Build and start the Docker containers
   - Generate application key
   - Run migrations
   - Set proper permissions
   - Cache configuration for better performance

3. Access the application at:
   - http://your-server-ip or domain

## Troubleshooting

- If you encounter permission issues, make sure the storage and bootstrap/cache directories are writable by the web server.
- Check the logs using `docker-compose logs -f` to diagnose any issues.
- If you need to access the container shells:
  ```
  docker-compose exec app bash
  docker-compose exec webserver sh
  docker-compose exec db bash
  ```
  
- For production containers:
  ```
  docker-compose -f docker-compose.prod.yml exec app bash
  docker-compose -f docker-compose.prod.yml exec webserver sh
  docker-compose -f docker-compose.prod.yml exec db bash
  ``` 