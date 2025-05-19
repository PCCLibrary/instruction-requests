#!/bin/bash
# Script to clear Laravel caches
echo "Clearing Laravel caches..."

# Change to the project directory
cd "$(dirname "$0")"

# Clear config cache
php artisan config:clear
echo "✅ Configuration cache cleared"

# Clear application cache
php artisan cache:clear
echo "✅ Application cache cleared"

# Clear compiled views
php artisan view:clear
echo "✅ Compiled views cleared"

# Clear route cache
php artisan route:clear
echo "✅ Route cache cleared"

echo "All caches cleared successfully!"
