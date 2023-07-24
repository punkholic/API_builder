# rm release/app/Http/Controllers/*
cd release && php artisan key:generate && php artisan migrate:fresh && php artisan serve
