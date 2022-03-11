<?php 


abstract class Constant {
    const PROJECT_ENV = ["RUN_SERVER", "MAKE_AUTH", "PROJECT_PATH"];

    const COMMANDS = [
        "CREATE_PROJECT" => "composer create-project codeigniter4/appstarter ",
        "MAKE_AUTH" => "composer require laravel/ui && php artisan ui:auth && php artisan ui:auth --views",
        "MAKE_MIGRATION" => "php spark make:migration PLACEHOLDER1 --table=PLACEHOLDER2",
        "MIGRATE_FRESH" => "php artisan migrate:fresh",
        "MAKE_MODEL" => "php spark make:model ",
        "MAKE_CONTROLLER" => "php spark make:controller ",
        "START_SERVER" => [
            "WITH_PORT" => "php artisan serve --port=",
            "WITHOUT_PORT" => "php artisan serve",
        ],
    ];
    
}
?>