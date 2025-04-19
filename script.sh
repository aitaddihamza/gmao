composer update --with-all-dependencies --ignore-platform-req=ext-intl --ignore-platform-req=ext-zip
npm install
cp .env.example .env
rm -rf database/database.sqlite
php artisan migrate
php artisan key:generate
php artisan db:seed
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=PieceSeeder
