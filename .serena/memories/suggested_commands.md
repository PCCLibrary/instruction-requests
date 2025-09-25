# Suggested Commands - Library Instruction System

## Laravel Artisan Commands

### Server & Development
```bash
php artisan serve                    # Start development server
php artisan tinker                   # Interactive REPL
php artisan route:list               # List all routes
php artisan queue:work               # Process queue jobs
php artisan queue:restart            # Restart queue workers
```

### Database
```bash
php artisan migrate                  # Run migrations
php artisan migrate:rollback         # Rollback last migration
php artisan migrate:fresh           # Drop all tables and re-run migrations
php artisan db:seed                  # Run database seeders
php artisan migrate:fresh --seed    # Fresh migration with seeders
```

### Cache Management
```bash
php artisan cache:clear              # Clear application cache
php artisan config:clear             # Clear config cache
php artisan route:clear              # Clear route cache
php artisan view:clear               # Clear compiled views
./clear-caches.sh                    # Clear all caches (custom script)
```

### Code Generation
```bash
php artisan make:controller ControllerName    # Create controller
php artisan make:model ModelName             # Create model
php artisan make:migration migration_name    # Create migration
php artisan make:livewire ComponentName      # Create Livewire component
php artisan make:test TestName               # Create test
php artisan make:seeder SeederName           # Create seeder
```

### Testing
```bash
php artisan test                     # Run all tests with Pest
php artisan test --filter TestName   # Run specific test
```

### Google Calendar Testing
```bash
php artisan test:google-calendar-attendees         # Test full calendar integration
php artisan diagnose:google-calendar-attendees     # Diagnose API issues
```

### Media Library
```bash
php artisan media:cleanup-temp       # Clean up temporary uploads
```

## Deployment Commands

### Environment Switching
```bash
./deploy.sh local                    # Deploy for local development
./deploy.sh testing                  # Deploy for test server
./deploy.sh production               # Deploy for production
```

### Queue Workers (Production)
```bash
sudo supervisorctl restart laravel-worker:*     # Restart queue workers
sudo supervisorctl status                        # Check worker status
```

## Frontend Commands

### NPM
```bash
npm install                          # Install dependencies
npm run dev                          # Start Vite dev server
npm run build                        # Build for production
npm run watch                        # Watch for changes
```

### Composer
```bash
composer install                     # Install PHP dependencies
composer update                      # Update dependencies
composer dump-autoload               # Regenerate autoload files
```

## Git Commands

### Standard Workflow
```bash
git status                           # Check status
git pull                             # Pull latest changes
git add .                            # Stage all changes
git commit -m "message"              # Commit changes
git push                             # Push to remote
```

## Custom Scripts

### Shell Scripts
```bash
./clear-caches.sh                    # Clear all Laravel caches
./copy-dropzone.sh                   # Copy Dropzone files
./deploy.sh [env]                    # Deploy to environment
./check_routes.sh                    # Check route conflicts
./check_duplicates.sh                # Check for duplicate code
```

## Troubleshooting Commands

### Permission Issues
```bash
chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

### Storage Links
```bash
php artisan storage:link             # Create storage symlink
```

### Package Discovery
```bash
php artisan package:discover         # Discover packages
rm bootstrap/cache/*.php             # Clear cached packages
```

## Darwin (macOS) Specific

### File Operations
```bash
ls -la                               # List all files with details
find . -name "pattern"               # Find files by pattern
grep -r "pattern" .                  # Search recursively
open .                               # Open current directory in Finder
```

### Process Management
```bash
ps aux | grep artisan                # Find artisan processes
lsof -ti:8000 | xargs kill          # Kill process on port 8000
```