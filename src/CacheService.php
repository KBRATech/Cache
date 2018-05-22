<?php

namespace Kbra\Cache;

use phpFastCache\CacheManager;
use phpFastCache\Core\Pool\ExtendedCacheItemPoolInterface;
use phpFastCache\Core\Item\ExtendedCacheItemInterface;


class CacheService
{
    /** @var ExtendedCacheItemPoolInterface */
    private $cachePool;

    /** @var array */
    private $settings;

    /**
     * @param array $settings
     */
    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @throws CacheServiceException
     */
    public function connect()
    {
        $tries = 0;
        $connectionException = null;

        do {
            $tries++;
            try {
                $this->cachePool = $this->getCachePool();
                return;
            } catch (\RedisException $e) {
                $connectionException = $e;
            }
        } while (
            !is_a($this->cachePool, 'ExtendedCacheItemPoolInterface')
            && $tries < $this->settings['maxRetries']
        );

        throw CacheServiceException::connectionFailed(
            $this->settings['driver'], $tries, $connectionException
        );
    }

    /**
     * @param string $name
     * @param mixed $value
     * @param array $options
     * @param array $tags
     * @param int $ttl
     * @return bool
     */
    public function set(
        $name,
        $value,
        array $options = [],
        array $tags = [],
        $ttl = null
    ) {
        $cacheItem = $this->getCacheItem($name, $options);

        if ($tags) {
            $cacheItem->addTags($this->normalizeTags($tags));
        }

        if (!$ttl || !is_int($ttl)) {
            $ttl = CacheManager::getDefaultConfig()['defaultTtl'];
        }

        return $this->setCacheItem($cacheItem, $value, $ttl);
    }

    /**
     * @param string $name
     * @param array $options
     * @return mixed|null
     */
    public function get($name, array $options = [])
    {
        $cacheItem = $this->getCacheItem($name, $options);

        return $cacheItem->get();
    }

    /**
     * @param array $tags
     * @return bool
     */
    public function clearTags(array $tags)
    {
        return $this->getCachePool()->deleteItemsByTags($this->normalizeTags($tags));
    }

    /**
     * @codeCoverageIgnore
     * @return ExtendedCacheItemPoolInterface
     */
    protected function getCachePool()
    {
        if ($this->cachePool) {
            return $this->cachePool;
        }

        CacheManager::setDefaultConfig($this->settings['config']);

        return CacheManager::getInstance($this->settings['driver']);
    }

    /**
     * @param string $name
     * @param array $options
     * @return ExtendedCacheItemInterface
     */
    private function getCacheItem($name, array $options = [])
    {
        $key = md5($name . json_encode($options));

        return $this->getCachePool()->getItem($key);
    }

    /**
     * @param ExtendedCacheItemInterface $cacheItem
     * @param mixed $value
     * @param int $ttl
     * @return bool
     */
    private function setCacheItem(ExtendedCacheItemInterface $cacheItem, $value, $ttl)
    {
        $cacheItem->set($value)
            ->expiresAfter($ttl);

        return $this->getCachePool()->save($cacheItem);
    }

    /**
     * @param array $tags
     * @return array
     */
    private function normalizeTags(array $tags)
    {
        $normalizedTags = [];

        foreach ($tags as $tag) {
            $normalizedTags[] = strtolower($tag);
        }

        return $normalizedTags;
    }
}
