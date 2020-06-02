<?php

/**
 * Bakame.Geolocation (https://github.com/bakame-php/geolocation/)
 * (c) Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Bakame\Geolocation;

final class CanNotGenerateGeolocation extends \InvalidArgumentException
{
    public static function dueToInvalidGeoHash(string $geoHash, \Throwable $exception): self
    {
        return new self('Fail instantiating a Geolocation instance using the geoHash `'.$geoHash.'`', 0, $exception);
    }
}
