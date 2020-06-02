<?php

/**
 * Bakame.Geolocation (https://github.com/bakame-php/geolocation/)
 * (c) Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Bakame\Geolocation;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Bakame\Geolocation\Geolocation
 */
final class GeolocationTest extends TestCase
{
    private Geolocation $location;

    private array $coordinatesOfAntwerp = [
        'lat' => 51.260197,
        'lng' => 4.402771,
        'geoHash' => 'u155s5wh2121'
    ];

    private const TIMEZONE = 'Europe/Brussels';

    public function setUp(): void
    {
        parent::setUp();

        date_default_timezone_set(self::TIMEZONE);

        $this->location = Geolocation::fromCoordinates($this->coordinatesOfAntwerp['lat'], $this->coordinatesOfAntwerp['lng']);
    }

    /**
     * @test
     */
    public function it_can_determine_sunrise_from_a_specific_datetime(): void
    {
        $now = new CarbonImmutable('2020-01-01 14:30:20');
        $sunrise = $this->location->sunrise($now);

        self::assertInstanceOf(CarbonImmutable::class, $sunrise);
        self::assertSame('2020-01-01 08:46:41', $sunrise->format('Y-m-d H:i:s'));
    }

    /**
     * @test
     */
    public function it_can_determine_zenith_from_a_specific_datetime(): void
    {
        $now = new Carbon('2020-01-01 14:30:20');
        $zenith = $this->location->zenith($now);

        self::assertSame('2020-01-01 12:45:42', $zenith->format('Y-m-d H:i:s'));
    }

    /**
     * @test
     */
    public function it_can_determine_sunset_from_a_specific_datetime(): void
    {
        $now = new Carbon('2020-01-01 14:30:20');
        $sunset = $this->location->sunset($now);

        self::assertInstanceOf(DateTimeImmutable::class, $sunset);
        self::assertSame('2020-01-01 16:44:43', $sunset->format('Y-m-d H:i:s'));
    }

    /**
     * @test
     */
    public function it_can_determine_if_the_sun_is_up(): void
    {
        $now = CarbonImmutable::now();

        $sunrise = $this->location->sunrise($now);

        self::assertInstanceOf(DateTimeImmutable::class, $sunrise);
        $sunriseBefore = $sunrise->sub(new DateInterval('PT1S'));
        $sunriseAfter = $sunrise->add(new DateInterval('PT1S'));

        self::assertFalse($this->location->isSunUp($sunriseBefore));
        self::assertTrue($this->location->isSunUp($sunrise));
        self::assertTrue($this->location->isSunUp($sunriseAfter));
    }

    /**
     * @test
     */
    public function it_can_determine_if_the_sun_is_down(): void
    {
        $now = CarbonImmutable::now();

        $sunset = $this->location->sunset($now);

        self::assertInstanceOf(DateTimeImmutable::class, $sunset);
        $sunsetBefore = $sunset->sub(new DateInterval('PT1S'));
        $sunsetAfter = $sunset->add(new DateInterval('PT1S'));

        self::assertTrue($this->location->isSunUp($sunsetBefore));
        self::assertFalse($this->location->isSunUp($sunset));
        self::assertFalse($this->location->isSunUp($sunsetAfter));
    }

    /**
     * @test
     */
    public function it_can_be_instantiated_from_geo_hash(): void
    {
        $now = CarbonImmutable::now();

        $localionCoord = Geolocation::fromCoordinates($this->coordinatesOfAntwerp['lat'], $this->coordinatesOfAntwerp['lng']);
        $sunsetCoord = $localionCoord->sunset($now);

        $locationHash = Geolocation::fromGeoHash($this->coordinatesOfAntwerp['geoHash']);
        $sunsetHash = $locationHash->sunset($now);

        self::assertEquals($sunsetCoord, $sunsetHash);
    }

    /**
     * @test
     */
    public function it_exposes_location_properties(): void
    {
        self::assertSame($this->coordinatesOfAntwerp['lat'], $this->location->latitude());
        self::assertSame($this->coordinatesOfAntwerp['lng'], $this->location->longitude());
        self::assertSame($this->coordinatesOfAntwerp['geoHash'], $this->location->geoHash());
    }

    /**
     * @test
     */
    public function it_returns_null_if_no_sunrise_is_possible(): void
    {
        $location = Geolocation::fromCoordinates(90, 0);

        self::assertNull($location->sunrise(new DateTime('2017-12-21')));
    }

    /**
     * @test
     */
    public function it_returns_null_if_no_sunset_is_possible(): void
    {
        $location = Geolocation::fromCoordinates(90, 0);

        self::assertNull($location->sunset(new DateTime('2017-12-21')));
    }

    /**
     * @test
     */
    public function it_returns_false_if_the_sun_can_not_rise(): void
    {
        $now = new DateTime('2017-12-21');
        $location = Geolocation::fromCoordinates(90, 0);

        self::assertFalse($location->isSunUp($now));
    }

    /**
     * @test
     */
    public function it_fails_to_generate_a_geo_location_if_the_geohash_is_invalid(): void
    {
        self::expectException(CanNotGenerateGeolocation::class);

        Geolocation::fromGeoHash('foobar');
    }
}
