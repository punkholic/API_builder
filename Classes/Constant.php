<?php 

abstract class Constant {
    const PROJECT_ENV = ["RUN_SERVER", "MAKE_AUTH", "PROJECT_PATH"];

    const COMMANDS = [
        "CREATE_PROJECT" => "composer create-project --prefer-dist laravel/laravel ",
        "MAKE_AUTH" => "composer require laravel/ui && php artisan ui:auth && php artisan ui:auth --views",
        "MAKE_MIGRATION" => "php artisan make:migration create_PLACEHOLDER1_table --table=PLACEHOLDER2",
        "MIGRATE_FRESH" => "php artisan migrate:fresh",
        "MAKE_MODEL" => "php artisan make:model ",
        "MAKE_CONTROLLER" => "php artisan make:controller ",
        "START_SERVER" => [
            "WITH_PORT" => "php artisan serve --port=",
            "WITHOUT_PORT" => "php artisan serve",
        ],
    ];
    
}
?>