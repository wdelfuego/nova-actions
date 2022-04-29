This package adds support for applying a global `DateTime` format to all action events displayed in the action logs of your Nova 4 resources, respecting the global `DateTime` format setting introduced by [wdelfuego/datetime](https://github.com/wdelfuego/nova-datetime). It will serve as a base for more extensions and improvements with regard to actions and action events in Laravel's [Nova 4](https://nova.laravel.com).

## Installation
```sh
composer require wdelfuego/nova-actions
```
  
## Usage

### Formatting `DateTime` fields in all action events globally
1. Make sure you've followed steps 1 and 2 of the [wdelfuego/datetime](https://github.com/wdelfuego/nova-datetime) package. 
	
	When the file `config/nova-datetime.php` exists in your project and specifies a `globalFormat`, you're ready for step 2.

2. In `config/nova.php`, at the top of the file, replace this use statement:

	```php
	use Laravel\Nova\Actions\ActionResource;
	```

	by this use statement:

	```php
	use Wdelfuego\Nova\Actions\ActionResource;
	```

That's it!

The 'Action Happened At' column displayed in the action logs of all of your Nova 4 resources are now shown in the global `DateTime` format you specified in `config/nova-datetime.php`.


## Support

For any problems, questions or remarks you might have, please open an issue on [GitHub](https://github.com/wdelfuego/nova-actions).