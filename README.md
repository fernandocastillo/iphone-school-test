# Back-end Developer Test
Fernando Castillo
desarrollo@freengers.com


## Installation
Install composer dependencies:

```sh
composer install
```

## Running test

This project has already an .env.testing file configured with sqlite for faster testing:

```sh
php artisan config:clear --env=testing
php artisan route:cache --env=testing;
php artisan test --env=testing
```

## License

MIT

**Free Software, Hell Yeah!**

