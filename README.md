# Get information on a position

This package uses geolocation to determine several properties around the sun position.

This package is a fork of [Spatie/Sun](https://github.com/spatie/sun)

## Installation

You can install the package via composer:

```bash
composer require bakame-php/geolocation
```

## Usage

To instantiate the `Bakame\Geolocation\Geolocation` class you need to use one of the following named constructor.

```php
use Bakame\Geolocation\Geolocation;

//Brussels coordinates and GeoHash
$bxLatitude = 50.8466;
$bxLongitude = 4.3528;
$bxGeohash = 'u151703dgt4z';

$location = GeoLocation::fromCoordinates($bxLatitude, $bxLongitude);
$location = Geolocation::fromGeoHash($bxGeohash);
```

On failed instantiation a `Bakame\Geolocation\CanNotGenerateGeolocation` exception is thrown.

- Latitudes below `-90.0` or above `90.0` degrees are capped, not wrapped.
- Longitudes below `-180.0` or above `180.0` degrees are wrapped.

### Get the time of zenith

You can get the time of the zenith.

```php
$location->zenith(); // returns an instance of \DateTimeImmutable
```

You can get the time of the zenith on a specific date by passing an object which implements `DateTimeInterface` to `zenith`

If the object extends `DateTimeImmutable` the return object will be of the same type.

```php
$carbon = CarbonImmutable::now();
$location->zenith($carbon); // returns an instance of \Carbon\CarbonImmutable
```

### Get the time of sunrise

You can get the time of the sunrise.

```php
$location->sunrise(); // returns an instance of \DateTimeImmutable
```

You can get the time of the sunrise on a specific date by passing an object which implements `DateTimeInterface` to `sunrise`

If the object extends `DateTimeImmutable` the return object will be of the same type.

```php
$carbon = CarbonImmutable::now();
$location->sunrise($carbon); // returns an instance of \Carbon\CarbonImmutable
```

If no sunrise information is available for a specific geolocation `null` is returned.

### Get the time of sunset

You can get the time of the sunset.

```php
$location->sunset(); // returns an instance of \DateTimeImmutable
```

You can get the time of the sunset on a specific date by passing an object which implements `DateTimeInterface` to `sunset`

If the object extends `DateTimeImmutable` the return object will be of the same type.

```php
$carbon = CarbonImmutable::now();

$location->sunset($carbon); // returns an instance of \Carbon\CarbonImmutable
```

If no sunset information is available for a specific geolocation `null` is returned.

### Determine if the sun is up

This is how you can determine if the sun is up:

```php
$location->isSunUp(); // returns a boolean
```

You can get determine if the sun is up at a specific moment by passing an instance of `DateTimeInterface` to `sunIsUp`

```php
$carbon = Carbon::now();

$location->sunIsUp($carbon); // returns a boolean
```

## Testing

``` bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email nyamsprod@gmail.com instead of using the issue tracker.

## Credits

- [Ignace Nyamagana Butera](https://github.com/nyamsprod)
- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
