composer install

php artisan serve

Use this command to create the Model from database table

php artisan infyom:api_scaffold User --fromTable --tableName=users --skip=scaffold_controller,scaffold_requests,scaffold_routes,views

Use this command to rollback the created Models

php artisan infyom:rollback User api_scaffold
