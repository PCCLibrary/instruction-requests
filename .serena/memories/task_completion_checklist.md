# Task Completion Checklist

## Before Committing Code

### 1. Code Quality
- [ ] Remove debug statements (`dd()`, `dump()`, `var_dump()`)
- [ ] Remove commented-out code
- [ ] Update/add PHPDoc blocks for new methods
- [ ] Check for proper type hints and return types
- [ ] Ensure consistent code formatting (4 spaces, no tabs)
- [ ] Remove unused imports/use statements

### 2. Testing
- [ ] Run test suite: `php artisan test`
- [ ] Test manually in browser if UI changes
- [ ] Test with different user roles/permissions
- [ ] Verify file uploads work (if applicable)
- [ ] Check mobile responsiveness (if UI changes)

### 3. Database
- [ ] Run migrations: `php artisan migrate`
- [ ] Check for migration rollback capability
- [ ] Seed test data if needed: `php artisan db:seed`
- [ ] Verify foreign key constraints

### 4. Frontend Assets
- [ ] Build assets: `npm run build` (for production)
- [ ] Check for console errors in browser
- [ ] Verify Alpine.js/Livewire functionality
- [ ] Test form validation

### 5. Cache Management
- [ ] Clear caches: `./clear-caches.sh` or:
  ```bash
  php artisan cache:clear
  php artisan config:clear
  php artisan route:clear
  php artisan view:clear
  ```
- [ ] Rebuild autoloader: `composer dump-autoload`

### 6. Code Review Self-Checks
- [ ] No hardcoded credentials or sensitive data
- [ ] Environment variables used for configuration
- [ ] Error handling implemented
- [ ] Logging added for important operations
- [ ] Security considerations addressed

### 7. Documentation
- [ ] Update README.md if significant changes
- [ ] Add inline comments for complex logic
- [ ] Update API documentation (if applicable)
- [ ] Document breaking changes

## Deployment Checklist

### Test Environment
```bash
# 1. Switch to testing environment
./deploy.sh testing

# 2. Run migrations
php artisan migrate

# 3. Clear caches
php artisan cache:clear
php artisan config:clear

# 4. Restart queue workers
sudo supervisorctl restart laravel-worker:*

# 5. Test functionality
```

### Production Environment
```bash
# 1. Switch to production environment
./deploy.sh production

# 2. Run migrations (with backup)
php artisan migrate --force

# 3. Clear and optimize caches
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. Rebuild autoloader
composer dump-autoload --optimize

# 5. Build frontend assets
npm run build

# 6. Restart queue workers
sudo supervisorctl restart laravel-worker:*

# 7. Verify deployment
```

## After Deployment

### Monitoring
- [ ] Check Laravel logs: `storage/logs/laravel.log`
- [ ] Monitor queue worker status: `sudo supervisorctl status`
- [ ] Verify email notifications working
- [ ] Test file uploads
- [ ] Check Google Calendar integration (if applicable)

### Rollback (if needed)
```bash
# 1. Revert Git changes
git revert [commit-hash]

# 2. Rollback database
php artisan migrate:rollback

# 3. Clear caches
./clear-caches.sh

# 4. Restart queue workers
sudo supervisorctl restart laravel-worker:*
```

## Common Issues and Fixes

### Queue Not Processing
```bash
# Check queue status
php artisan queue:monitor

# Restart queue workers
sudo supervisorctl restart laravel-worker:*

# Flush failed jobs
php artisan queue:flush
```

### Permission Issues
```bash
# Fix storage permissions
chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

### Missing Storage Link
```bash
php artisan storage:link
```

### Stale Lock Issues
```bash
# Check for stale locks
php artisan tinker
>>> InstructionRequests::where('locked', true)->get()

# Force unlock (admin only)
# Use admin panel UI or:
>>> InstructionRequests::find($id)->unlock(true)
```

## Environment-Specific Notes

### Local Development
- Use `.env.local` configuration
- Hot module replacement: `npm run dev`
- Debug mode enabled
- Detailed error messages

### Testing Server
- Use `.env.testing` configuration
- Build assets: `npm run build`
- Test email notifications
- Verify SAML authentication

### Production Server
- Use `.env.production` configuration
- Optimize everything (cache, autoload, build)
- Debug mode disabled
- Monitor error logs closely
- Schedule backup before major changes