cd /var/www/html/secure
git add .
git reset --hard
git pull origin
composer install --no-interaction
php artisan migrate
