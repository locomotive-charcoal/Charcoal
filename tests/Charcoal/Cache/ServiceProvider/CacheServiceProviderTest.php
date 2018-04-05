<?php

namespace Charcoal\Tests\Cache\ServiceProvider;

// From PSR-3
use Psr\Log\NullLogger;

// From Pimple
use Pimple\Container;

// From 'tedivm/stash'
use Stash\DriverList;
use Stash\Interfaces\DriverInterface;
use Stash\Interfaces\PoolInterface;

// From 'charcoal-cache'
use Charcoal\Tests\AbstractTestCase;
use Charcoal\Cache\CacheBuilder;
use Charcoal\Cache\CacheConfig;
use Charcoal\Cache\ServiceProvider\CacheServiceProvider;

/**
 * Test CacheServiceProvider
 *
 * @coversDefaultClass \Charcoal\Cache\ServiceProvider\CacheServiceProvider
 */
class CacheServiceProviderTest extends AbstractTestCase
{
    /**
     * @covers ::register
     */
    public function testProvider()
    {
        $container = $this->providerFactory();

        $this->assertArrayHasKey('cache/config', $container);
        $this->assertInstanceOf(CacheConfig::class, $container['cache/config']);

        $this->assertArrayHasKey('cache/available-drivers', $container);
        $this->assertTrue($this->isAccessible($container['cache/available-drivers']));

        $this->assertArrayHasKey('cache/drivers', $container);
        $this->assertTrue($this->isAccessible($container['cache/drivers']));

        $this->assertArrayHasKey('cache/driver', $container);
        $this->assertInstanceOf(DriverInterface::class, $container['cache/driver']);

        $this->assertArrayHasKey('cache/builder', $container);
        $this->assertInstanceOf(CacheBuilder::class, $container['cache/builder']);

        $this->assertArrayHasKey('cache/factory', $container);
        $this->assertInstanceOf(PoolInterface::class, $container['cache/factory']);

        $this->assertArrayHasKey('cache', $container);
        $this->assertInstanceOf(PoolInterface::class, $container['cache']);
    }

    /**
     * Test "cache/drivers"; basic drivers are instances of {@see DriverInterface}.
     *
     * @covers ::register
     */
    public function testBasicDriverInstances()
    {
        $container = $this->providerFactory();

        $driverMap = [
            'BlackHole'  => 'noop',
            'Ephemeral'  => 'memory',
            'FileSystem' => 'file',
        ];

        $driverClassNames = DriverList::getAllDrivers();
        $driverCollection = $container['cache/drivers'];

        foreach ($driverMap as $driverName => $driverKey) {
            if (isset($driverClassNames[$driverName])) {
                $className = $driverClassNames[$driverName];
                $driver    = $driverCollection[$driverKey];
                $this->assertInstanceOf($className, $driver);
            }
        }
    }

    /**
     * Test "cache/drivers"; vendor drivers are instances of {@see DriverInterface}.
     *
     * @covers ::registerService
     */
    public function testAvailableVendorDriverInstances()
    {
        $container = $this->providerFactory();

        $driverMap = [
            'Apc'      => 'apc',
            'Memcache' => 'memcache',
            'Redis'    => 'redis',
            'SQLite'   => 'db',
        ];

        $driverClassNames = DriverList::getAllDrivers();
        $driverCollection = $container['cache/drivers'];

        foreach ($driverMap as $driverName => $driverKey) {
            if (isset($driverClassNames[$driverName])) {
                $className = $driverClassNames[$driverName];

                if ($className::isAvailable()) {
                    $driver = $driverCollection[$driverKey];
                    $this->assertInstanceOf($className, $driver);
                } else {
                    $driver = $driverCollection[$driverKey];
                    $this->assertNull($driver);
                }
            }
        }
    }

    /**
     * Test "cache/drivers"; unavailable vendor drivers return NULL.
     *
     * @covers ::registerService
     */
    public function testUnavailableVendorDriverInstances()
    {
        $container = $this->providerFactory();

        // Emptied to fake unavailability
        $container['cache/available-drivers'] = [];

        $driverMap = [
            'Apc'      => 'apc',
            'Memcache' => 'memcache',
            'Redis'    => 'redis',
            'SQLite'   => 'db',
        ];

        $driverClassNames = DriverList::getAllDrivers();
        $driverCollection = $container['cache/drivers'];

        foreach ($driverMap as $driverName => $driverKey) {
            if (isset($driverClassNames[$driverName])) {
                $driver = $driverCollection[$driverKey];
                $this->assertNull($driver);
            }
        }
    }

    /**
     * Assert "cache/driver" resolves as expected.
     *
     * @covers ::register
     *
     * @dataProvider provideConfigsForMainDriver
     *
     * @param  string $className   The expected driver class name.
     * @param  array  $cacheConfig The cache configset to resolve the main driver.
     * @return void
     */
    public function testMainDriverInstance($className, array $cacheConfig)
    {
        $container = $this->providerFactory([
            'config' => [
                'cache' => $cacheConfig
            ]
        ]);

        $this->assertInstanceOf($className, $container['cache/driver']);
    }

    /**
     * Provide data for testing the "cache/driver" service.
     *
     * @used-by self::testMainDriverInstance()
     * @return  array
     */
    public function provideConfigsForMainDriver()
    {
        $driverClassNames = DriverList::getAvailableDrivers();

        return [
            'Cache: Disabled' => [
                $driverClassNames['Ephemeral'],
                [
                    'active' => false
                ]
            ],
            'Cache: Default Type' => [
                $driverClassNames['Ephemeral'],
                [
                ]
            ],
            'Cache: Fallback Type' => [
                $driverClassNames['Ephemeral'],
                [
                    'types' => []
                ]
            ],
            'Cache: Chosen Type' => [
                $driverClassNames['BlackHole'],
                [
                    'types' => [ 'noop' ]
                ]
            ],
        ];
    }

    /**
     * Return all the keys or a subset of the keys of an array.
     *
     * @param  mixed $value The variable containing keys to return.
     * @return array
     */
    public function getKeys($value)
    {
        if (is_array($value)) {
            return array_keys($value);
        } elseif (is_callable([ $value, 'keys' ])) {
            return $value->keys();
        }

        return [];
    }

    /**
     * Determine whether the given value is array accessible.
     *
     * @param  mixed $value The variable being evaluated.
     * @return boolean
     */
    public function isAccessible($value)
    {
        return is_array($value) || $value instanceof \ArrayAccess;
    }

    /**
     * Create a new Container instance.
     *
     * @param  array $args Parameters for the initialization of a Container.
     * @return Container
     */
    public function providerFactory(array $args = [])
    {
        $container = new Container($args);

        if (!isset($container['logger'])) {
            $container['logger'] = new NullLogger();
        }

        $provider  = new CacheServiceProvider();
        $provider->register($container);

        return $container;
    }
}
