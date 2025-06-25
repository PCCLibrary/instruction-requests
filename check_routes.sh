#!/bin/bash
echo "Checking Laravel Routes..."
php artisan route:list --name=instruction
echo ""
echo "Checking for edit routes specifically..."
php artisan route:list | grep edit
