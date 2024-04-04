#============================================================
#   php artisan route, config, composer dumpautoload clear command
#============================================================

php artisan route:cache
php artisan config:cache
php artisan view:cache
php artisan optimize:clear
php artisan clear-compiled  
composer dumpautoload -o
