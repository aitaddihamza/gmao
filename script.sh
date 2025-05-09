composer update --with-all-dependencies --ignore-platform-req=ext-intl --ignore-platform-req=ext-zip
npm install
mv env .env
mkdir -p storage/app/public/reports
mkdir -p storage/app/public/rapports-tickets
mkdir -p storage/app/public/manuels-pieces
php artisan db:wipe
php artisan migrate
php artisan db:seed
php artisan key:generate
php artisan storage:link
