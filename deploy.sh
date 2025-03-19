#!/bin/bash

# Library Instruction Request System Deployment Script
# Usage: ./deploy.sh [local|testing|production]

# Determine environment from argument
ENV=$1
if [ -z "$ENV" ]; then
    echo "Usage: ./deploy.sh [local|testing|production]"
    exit 1
fi

# Copy appropriate .env file
if [ "$ENV" == "local" ]; then
    echo "Deploying for local Docker environment..."
    cp .env.local .env
elif [ "$ENV" == "testing" ]; then
    echo "Deploying for PCC test server..."
    cp .env.testing .env
elif [ "$ENV" == "production" ]; then
    echo "Deploying for PCC production server..."
    cp .env.production .env
else
    echo "Invalid environment specified. Use local, testing, or production."
    exit 1
fi

# Clear caches
echo "Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Create storage directories
echo "Creating storage directories..."
mkdir -p storage/app/public/uploads/temp
mkdir -p public/storage

# Create storage link
echo "Creating storage link..."
php artisan storage:link

# Set permissions (for non-local environments)
if [ "$ENV" != "local" ]; then
    echo "Setting file permissions..."
    sudo chown -R www-data:www-data storage
    sudo chmod -R 775 storage
    sudo chown -R www-data:www-data public/storage
    sudo chmod -R 775 public/storage
fi

# Optimize for production
if [ "$ENV" == "production" ]; then
    echo "Optimizing for production..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

# Flush and restart queues
echo "Flushing and restarting queues..."
php artisan queue:flush
php artisan queue:restart

# Run database migrations (optional)
if [ "$2" == "--migrate" ]; then
    echo "Running database migrations..."
    php artisan migrate --force
fi

echo "Deployment for $ENV environment completed!"
echo "To verify the configuration, visit the /env-test URL (not available in production)."