composer update --with-all-dependencies --ignore-platform-req=ext-intl --ignore-platform-req=ext-zip
npm install
cp .env.example .env
php artisan migrate:fresh --seed
php artisan key:generate
php artisan serve
