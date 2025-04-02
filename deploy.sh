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
    WEB_USER="www-data"
    ADMIN_GROUP="www-data"
elif [ "$ENV" == "testing" ]; then
    echo "Deploying for PCC test server..."
    cp .env.testing .env
    WEB_USER="apache"
    ADMIN_GROUP="wheel"  # Using wheel as the admin group
elif [ "$ENV" == "production" ]; then
    echo "Deploying for PCC production server..."
    cp .env.production .env
    WEB_USER="apache"
    ADMIN_GROUP="wheel"  # Using wheel as the admin group
else
    echo "Invalid environment specified. Use local, testing, or production."
    exit 1
fi

# Create storage directories first
echo "Creating storage directories..."
mkdir -p storage/app/public/uploads/temp
mkdir -p storage/logs
mkdir -p bootstrap/cache
mkdir -p public/storage

# Set permissions with wheel/admin group
echo "Setting file permissions for web user and admin group..."
sudo chown -R $WEB_USER:$ADMIN_GROUP storage
sudo chmod -R 775 storage
sudo chmod g+s storage  # Set SGID bit so new files inherit group
sudo chmod g+s storage/logs

sudo chown -R $WEB_USER:$ADMIN_GROUP bootstrap/cache
sudo chmod -R 775 bootstrap/cache

sudo chown -R $WEB_USER:$ADMIN_GROUP public/storage
sudo chmod -R 775 public/storage

# Make specific log file writable if it exists
if [ -f storage/logs/laravel.log ]; then
    sudo chown $WEB_USER:$ADMIN_GROUP storage/logs/laravel.log
    sudo chmod 664 storage/logs/laravel.log
fi

# Configure SELinux if present (for test/production servers)
if [ "$ENV" != "local" ] && [ -x "$(command -v sestatus)" ] && sestatus | grep -q "enabled"; then
    echo "Configuring SELinux context..."
    # Set context
    sudo semanage fcontext -a -t httpd_sys_rw_content_t "$(pwd)/storage(/.*)?"
    sudo semanage fcontext -a -t httpd_sys_rw_content_t "$(pwd)/bootstrap/cache(/.*)?"
    sudo semanage fcontext -a -t httpd_sys_rw_content_t "$(pwd)/public/storage(/.*)?"

    # Apply context
    sudo restorecon -Rv "$(pwd)/storage"
    sudo restorecon -Rv "$(pwd)/bootstrap/cache"
    sudo restorecon -Rv "$(pwd)/public/storage"

    # Set additional SELinux booleans if needed
    sudo setsebool -P httpd_unified 1
    sudo setsebool -P httpd_can_network_connect 1
fi

# Create storage link
echo "Creating storage link..."
php artisan storage:link || sudo -u $WEB_USER php artisan storage:link

# Try clearing caches directly first,
# falling back to sudo if there are permission issues
echo "Clearing caches..."
php artisan config:clear || sudo -u $WEB_USER php artisan config:clear
php artisan cache:clear || sudo -u $WEB_USER php artisan cache:clear
php artisan route:clear || sudo -u $WEB_USER php artisan route:clear
php artisan view:clear || sudo -u $WEB_USER php artisan view:clear
php artisan optimize:clear || sudo -u $WEB_USER php artisan optimize:clear

# Optimize for production
if [ "$ENV" == "production" ]; then
    echo "Optimizing for production..."
    php artisan config:cache || sudo -u $WEB_USER php artisan config:cache
    php artisan route:cache || sudo -u $WEB_USER php artisan route:cache
    php artisan view:cache || sudo -u $WEB_USER php artisan view:cache
fi

# Flush and restart queues
echo "Flushing and restarting queues..."
php artisan queue:flush || sudo -u $WEB_USER php artisan queue:flush
php artisan queue:restart || sudo -u $WEB_USER php artisan queue:restart

# Run database migrations (optional)
if [ "$2" == "--migrate" ]; then
    echo "Running database migrations..."
    php artisan migrate --force || sudo -u $WEB_USER php artisan migrate --force
fi

# Restart supervisor with improved handling
if [ -x "$(command -v supervisorctl)" ]; then
    echo "Restarting supervisor queue workers..."

    # Check if supervisor is running first
    if sudo systemctl is-active --quiet supervisord; then
        echo "Supervisor is running, restarting workers..."

        # Set a timeout for the restart command
        timeout 10s sudo supervisorctl restart laravel-worker:* || {
            echo "Supervisor restart command timed out. Continuing anyway."
        }
    else
        echo "Supervisor service is not running. Starting it..."
        sudo systemctl start supervisord

        # Wait a moment for the service to start
        sleep 2

        # Try to restart workers
        timeout 10s sudo supervisorctl restart laravel-worker:* || {
            echo "Supervisor restart command timed out. Continuing anyway."
        }
    fi
else
    echo "Supervisor not found, skipping queue worker restart."
fi

# Final permission fix to ensure all files are accessible
echo "Final permission check and fix..."
sudo find storage -type d -exec chmod 775 {} \;
sudo find storage -type f -exec chmod 664 {} \;
sudo find bootstrap/cache -type d -exec chmod 775 {} \;
sudo find bootstrap/cache -type f -exec chmod 664 {} \;

echo "Deployment for $ENV environment completed!"
echo "To verify the configuration, visit the /env-test URL (not available in production)."
