# Docker Setup for Laravel React Inertia Project

This document provides an overview of the Docker setup for this Laravel React Inertia project.

## File Structure

- `Dockerfile` - Basic Dockerfile for development
- `Dockerfile.prod` - Multi-stage build Dockerfile for production
- `docker-compose.yml` - Docker Compose file for development
- `docker-compose.prod.yml` - Docker Compose file for production
- `docker-setup.sh` - Setup script for development
- `docker-setup-prod.sh` - Setup script for production
- `docker-env.sh` - Unified script to set up either development or production environment
- `nginx/conf.d/app.conf` - Nginx configuration for the application
- `nginx/Dockerfile` - Dockerfile for Nginx
- `.env.example` - Example environment file for development
- `.env.production` - Example environment file for production

## Container Architecture

The Docker setup consists of three main services:

1. **app** - PHP-FPM container with Laravel application
   - Based on PHP 8.2
   - Includes Composer and Node.js for building assets
   - Runs the Laravel application

2. **webserver** - Nginx web server
   - Serves the application
   - Handles static files
   - Proxies PHP requests to the app container

3. **db** - MySQL database server
   - Stores application data
   - Persists data through Docker volumes

## Development vs Production

### Development Environment
- Uses `Dockerfile` and `docker-compose.yml`
- Mounts local directories for live code changes
- Includes development dependencies
- Debugging enabled

### Production Environment
- Uses `Dockerfile.prod` and `docker-compose.prod.yml`
- Multi-stage build for smaller image size
- Optimized for performance
- No development dependencies
- Configuration caching enabled

## Quick Start

Use the unified script to set up either environment:

```bash
# For development
./docker-env.sh dev

# For production
./docker-env.sh prod
```

## Manual Setup

See the `DOCKER_README.md` file for detailed manual setup instructions.

## Customization

You can customize the Docker setup by:

1. Modifying the Dockerfiles to add additional extensions or software
2. Updating the docker-compose files to add additional services
3. Adjusting the Nginx configuration in `nginx/conf.d/app.conf`
4. Modifying the environment variables in `.env.example` or `.env.production` 