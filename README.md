![simple-permissions Banner](docs/images/banner.jpg)


# Simple permission and role system for Laravel. Supports enums.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/guava/simple-permissions-for-laravel.svg?style=flat-square)](https://packagist.org/packages/guava/simple-permissions-for-laravel)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/guava/simple-permissions-for-laravel/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/guava/simple-permissions-for-laravel/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/guava/simple-permissions-for-laravel/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/guava/simple-permissions-for-laravel/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/guava/simple-permissions-for-laravel.svg?style=flat-square)](https://packagist.org/packages/guava/simple-permissions-for-laravel)

This is an opinionated simple permissions & roles system for Laravel. It allows you to define roles and permissions as PHP classes and enums directly in your codebase. This allows for out of the box auto-completion support and superb developer experience.

## Showcase

This is where your screenshots and videos should go. Remember to add them, so people see what your plugin does.

## Support us

Your support is key to the continual advancement of our plugin. We appreciate every user who has contributed to our journey so far.

While our plugin is available for all to use, if you are utilizing it for commercial purposes and believe it adds significant value to your business, we kindly ask you to consider supporting us through GitHub Sponsors. This sponsorship will assist us in continuous development and maintenance to keep our plugin robust and up-to-date. Any amount you contribute will greatly help towards reaching our goals. Join us in making this plugin even better and driving further innovation.

## Installation

You can install the package via composer:

```bash
composer require guava/simple-permissions
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="simple-permissions-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="simple-permissions-config"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

### Model setup
You need to add the `HasAccessControl` trait to your models that you want to add roles and permissions to.

This will add all necesary relationships and methods to start using access control.
```php
use Guava\SimplePermissions\Concerns\HasAccessControl;

public class User extends Model
{
    use HasAccessControl;
    
    // ...
}
```

### Creating permissions
Permissions can be created using an artisan command.
Let's say you have a model `Post` and want to create permissions for handling access to the Post resource.

Simply run:
```bash
php artisan make:permission PostPermissions
```

This will create a new enum in `App\Auth\Permissions\PostPermissions` with some predefined CRUD permissions:

```php
public enum PostPermissions: string implements \Guava\SimplePermissions\Contracts\Permission 
{
    case VIEW = 'view';
    // ...Other redefined permissions
}
```

### Creating Roles
Roles can be created using an artisan command.

Simply run:
```bash
php artisan make:role SuperAdmin
```

This will create a new role in `App\Auth\Roles\SuperAdmin`:

```php
public class SuperAdmin implements \Guava\SimplePermissions\Contracts\Role
{

    public function permissions() : array
    {
        return [
            // Add permissions here
            // Either one by one, such as:
            PostPermissions::VIEW,
            
            // or all at once:
           ...PostPermissions::cases()
        ];
    }
}
```

### Checking if a user has a permission
You can use Laravel's built-in methods to check permissions:

For example if a user has permissions to view a post, you could do:
```php
$user->can(PostPermissions::VIEW)
```

### FilamentPHP integration
All you need to do in order to add access control to your filament resources is to implement the `HasAuthorization` trait in your resource and define the Permission enum.

```php
use Guava\SimplePermissions\Concerns\HasAuthorization;
use App\Auth\Permissions\PostPermissions;

public class PostResource extends Resource
{
    use HasAuthorization;
    
    protected static string $permissions = PostPermissions::class;
    
    // ...
}
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Lukas Frey](https://github.com/GuavaCZ)
- [All Contributors](../../contributors)
- Spatie - Our package simple-permissions-for-laravel is a modified version of [Spatie's Package SimplePermissions](https://github.com/spatie/package-simple-permissions-for-laravel-laravel)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
