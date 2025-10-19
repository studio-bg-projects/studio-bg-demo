# Laravel Guidelines

This directory contains the Laravel 11 application.

- Requires PHP 8.3 and the Composer dependencies
- Use PSR-12 coding style with two-space indentation
- There is no test suite. You can check syntax with `php -l <file>`
- Run most commands via the `php-fpm` Docker service
- No automated tests or linting tasks are configured

## Language

- All user-facing messages should be in Bulgarian
- Ensure each comment is written on English

## Recommends

- Try not to use `old()` in the views

## Models & migrations

- The project uses MySQL
- Database fields use camelCase
- When creating migrations, include camelCase timestamps `createdAt` and `updatedAt`
- When adding JSON fields, always cast them to `array` in the model
- When creating or updating a model, always describe its properties at the top of the class. Use the @property annotation and explain each field in Bulgarian, for example: `@property boolean $isActive - дали продуктът е активен или не`

## Router permissions

- Permissions are defined in [PermissionsService.php](app/Services/PermissionsService.php)
- Add new permissions to [web.php](routes/web.php) as needed
- [CheckUserPermissions.php](app/Http/Middleware/CheckUserPermissions.php) ensures the user has the required permissions
