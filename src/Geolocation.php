<?php

/**
 * Bakame.Geolocation (https://github.com/bakame-php/geolocation/)
 * (c) Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Bakame\Geolocation;

use League\Geotools\Coordinate\Coordinate;
use League\Geotools\Coordinate\CoordinateInterface;
use League\Geotools\Geohash\Geohash;
use function date_sun_info;
use function date_sunrise;
use function date_sunset;
use const SUNFUNCS_RET_TIMESTAMP;

final class Geolocation
{
    private float $latitude;

    private float $longitude;

    private string $geoHash;

    private function __construct(float $latitude, float $longitude, string $geoHash)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->geoHash = $geoHash;
    }

    public static function fromGeotools(CoordinateInterface $coordinate): self
    {
        /** @var Geohash $geoHashService */
        $geoHashService = (new Geohash())->encode($coordinate);
        $geoHash = $geoHashService->getGeohash();

        return new self($coordinate->getLatitude(), $coordinate->getLongitude(), $geoHash);
    }

    public static function fromCoordinates(float $latitude, float $longitude): self
    {
        return self::fromGeotools(new Coordinate([$latitude, $longitude]));
    }

    /**
     * @throws CanNotGenerateGeolocation If the geoHash is invalid
     */
    public static function fromGeoHash(string $geoHash): self
    {
        try {
            /** @var Geohash $geoHashService */
            $geoHashService = (new Geohash())->decode($geoHash);
        } catch (\Throwable $exception) {
            throw CanNotGenerateGeolocation::dueToInvalidGeoHash($geoHash, $exception);
        }

        $coordinate = $geoHashService->getCoordinate();

        return new self($coordinate->getLatitude(), $coordinate->getLongitude(), $geoHash);
    }

    public function latitude(): float
    {
        return $this->latitude;
    }

    public function longitude(): float
    {
        return $this->longitude;
    }

    public function geoHash(): string
    {
        return $this->geoHash;
    }

    public function sunrise(\DateTimeInterface $date = null): ?\DateTimeImmutable
    {
        $date = $this->filterDate($date);
        $timezone = $date->getTimezone();
        /** @var int|false $timestamp */
        $timestamp = date_sunrise(
            $date->getTimestamp(),
            SUNFUNCS_RET_TIMESTAMP,
            $this->latitude,
            $this->longitude
        );

        if (false === $timestamp) {
            return null;
        }

        return $date->setTimestamp($timestamp)->setTimezone($timezone);
    }

    /**
     * @psalm-suppress ArgumentTypeCoercion
     */
    private function filterDate(\DateTimeInterface $date = null): \DateTimeImmutable
    {
        $date = $date ?? new \DateTimeImmutable();
        if ($date instanceof \DateTimeImmutable) {
            return $date;
        }

        return \DateTimeImmutable::createFromMutable($date);
    }

    public function zenith(\DateTimeInterface $date = null): \DateTimeImmutable
    {
        $date = $this->filterDate($date);
        $timezone = $date->getTimezone();
        /** @var int $timestamp */
        $timestamp = date_sun_info(
            $date->getTimestamp(),
            $this->latitude,
            $this->longitude
        )['transit'];

        return $date->setTimestamp($timestamp)->setTimezone($timezone);
    }

    public function sunset(\DateTimeInterface $date = null): ?\DateTimeImmutable
    {
        $date = $this->filterDate($date);
        $timezone = $date->getTimezone();
        /** @var int|false $timestamp */
        $timestamp = date_sunset(
            $date->getTimestamp(),
            SUNFUNCS_RET_TIMESTAMP,
            $this->latitude,
            $this->longitude
        );

        if (false === $timestamp) {
            return null;
        }

        return $date->setTimestamp($timestamp)->setTimezone($timezone);
    }

    public function isSunUp(\DateTimeInterface $date = null): bool
    {
        $date = $date ?? new \DateTimeImmutable();
        $sunrise = $this->sunrise($date);
        if (null === $sunrise) {
            return false;
        }

        $sunset = $this->sunset($date);

        return null === $sunset
            || ($date >= $sunrise && $date < $sunset);
    }
}
