<?php

namespace Kbra\Cache\Tests;

use Kbra\Cache\CacheService;
use PHPUnit\Framework\TestCase;
use phpFastCache\Core\Pool\ExtendedCacheItemPoolInterface;
use Psr\Cache\CacheItemInterface;
use PHPUnit\Framework\MockObject\MockObject;
use phpFastCache\Helper\TestHelper;

class CacheServiceTest extends TestCase
{
    /** @var ExtendedCacheItemPoolInterface */
    private $cachePool;

    /** @var CacheItemInterface|MockObject */
    private $cacheItem;

    /** @var CacheService */
    private $cacheService;

    public function setUp()
    {
        $settings = [
            'driver' => 'devnull',
            'config' => [],
        ];

        $this->cacheService = new CacheService($settings);

        $this->cachePool = $this->createMock(ExtendedCacheItemPoolInterface::class);
        $this->cacheItem = $this->createMock(CacheItemInterface::class);

        $this->cacheService->setCachePool($this->cachePool);
    }

    public function testSet()
    {
        $name = 'this is a test';
        $options = ['this','is','a','test'];
        $key = md5($name . json_encode($options));
        $value = 'this test is super awesome';

        $this->cachePool
            ->expects($this->once())
            ->method('getItem')
            ->with($this->identicalTo($key))
            ->will($this->returnValue($this->cacheItem));

        $this->cacheItem
            ->expects($this->once())
            ->method('set')
            ->with($this->identicalTo($value))
            ->will($this->returnValue($this->cacheItem));

        $this->cacheItem
            ->expects($this->once())
            ->method('expiresAfter')
            ->with($this->identicalTo(CacheService::CACHE_TIMEOUT_IN_SECONDS));

        $this->cachePool
            ->expects($this->once())
            ->method('save')
            ->will($this->returnValue(true));

        $result = $this->cacheService->set($name, $value, $options);

        $this->assertTrue($result);
    }

    public function testGet()
    {
        $name = 'this is a test';
        $options = ['this','is','a','test'];
        $key = md5($name . json_encode($options));

        $this->cachePool
            ->expects($this->once())
            ->method('getItem')
            ->with($this->identicalTo($key))
            ->will($this->returnValue($this->cacheItem));

        $this->cacheService->get($name, $options);
    }

}
