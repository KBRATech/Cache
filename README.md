# KBRA Cache

A simple PHP cache library

## Getting Started

### Prerequisites

* PHP 5.6 or 7
* Some [caching driver](https://github.com/PHPSocialNetwork/phpfastcache#supported-drivers-at-this-day-) supported by phpfastcache

### Installing

Install with composer:

```
composer require kbra/cache
```

Set the configuration based on your cache driver. Using Redis, your config might look something like this:

```
$settings = [
    'driver' => 'redis',
    'maxRetries' => 3,
    'config' => [
        'defaultTtl' => 900,
        'host' => 'cache.example.com',
        'port' => 6379,
        'database' => 11,
        'password' => 'SuperSecretPassword',
        'timeout' => 3,
    ],
];

$cacheService = new CacheService($settings);
```

*Right now this library only properly handles connection errors from Redis*

You may choose to connect your cache driver manually:

```
$cacheService->connect();
```

But if you don't, a connection will be attempted the first time it is needed.

To save data to the cache:

```
$cacheService->set('some-data', $data);
```

And to retrieve it from the cache:

```
$data = $cacheService->get('some-data');
```

You can pass some extra options when saving data to the cache. A unique cache key will be generated based on the name and options passed. For example, if you wanted to cache a database query with some parameters:

```
$query = "SELECT * FROM table WHERE thing = :thing";
$params = [':thing' => 'something'];
$result = $pdo->fetchAll($query, $params);

$cacheService->set($query, $result, $params);
```

And to get the cached result:

```
$result = $cacheService->get($query, $params);
```

You can also save some tags associated with the cache data, which can be used later to remove items from the cache:

```
$tags = [$userName, 'portfolio'];
$cacheService->set($query, $result, $params, $tags);

// clear ALL caches tagged with 'portfolio'
$cacheService->clearTags('portfolio');
```

## Running the tests

`composer test` or `phpunit`

## Built With

* [phpfastcache](https://github.com/PHPSocialNetwork/phpfastcache) - A PHP high-performance backend cache system

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct, and the process for submitting pull requests to us.

## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/KBRATech/Cache/releases).

## Authors

* **Erik Hierhager** - [ehierhager@kbra.com](mailto:ehierhager@kbra.com)

See also the list of [contributors](https://github.com/KBRATech/Cache/contributors) who participated in this project.

## License

This project is licensed under the Apache 2.0 License - see the [LICENSE](LICENSE) file for details

## Acknowledgments

* Kroll Bond Rating Agency, Inc.
* Rubber ducks
