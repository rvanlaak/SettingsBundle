<?php

/**
 * This file is part of the DmishhSettingsBundle package.
 * (c) 2013 Dmitriy Scherbina <http://dmishh.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmishh\SettingsBundle\Manager;

use Dmishh\SettingsBundle\Entity\SettingsOwnerInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * @author Tobias Nyholm
 */
class CachedSettingsManager implements SettingsManagerInterface
{
    const PREFIX = 'dmishh_settings_%s_%s';

    /**
     * @var CacheItemPoolInterface
     */
    private $storage;

    /**
     * @var SettingsManagerInterface
     */
    private $settingsManager;

    /**
     * @var int
     */
    private $cacheLifeTime;

    public function __construct(SettingsManagerInterface $settingsManager, CacheItemPoolInterface $storage, $cacheLifeTime)
    {
        $this->settingsManager = $settingsManager;
        $this->storage = $storage;
        $this->cacheLifeTime = $cacheLifeTime;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $name, ?SettingsOwnerInterface $owner = null, $default = null)
    {
        if (null !== $cached = $this->fetchFromCache($name, $owner)) {
            return $cached;
        }

        $value = $this->settingsManager->get($name, $owner, $default);
        $this->storeInCache($name, $value, $owner);

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function all(?SettingsOwnerInterface $owner = null): array
    {
        if (null !== $cached = $this->fetchFromCache(null, $owner)) {
            return $cached;
        }

        $value = $this->settingsManager->all($owner);
        $this->storeInCache(null, $value, $owner);

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $name, $value, ?SettingsOwnerInterface $owner = null): void
    {
        $this->invalidateCache($name, $owner);
        $this->invalidateCache(null, $owner);

        $this->settingsManager->set($name, $value, $owner);
    }

    /**
     * {@inheritdoc}
     */
    public function setMany(array $settings, ?SettingsOwnerInterface $owner = null): void
    {
        foreach ($settings as $key => $value) {
            $this->invalidateCache($key, $owner);
        }
        $this->invalidateCache(null, $owner);

        $this->settingsManager->setMany($settings, $owner);
    }

    /**
     * {@inheritdoc}
     */
    public function clear(string $name, ?SettingsOwnerInterface $owner = null): void
    {
        $this->invalidateCache($name, $owner);
        $this->invalidateCache(null, $owner);

        $this->settingsManager->clear($name, $owner);
    }

    /**
     * @return bool TRUE if the cache entry was successfully deleted, FALSE otherwise
     */
    protected function invalidateCache(?string $name, ?SettingsOwnerInterface $owner = null): bool
    {
        return $this->storage->deleteItem($this->getCacheKey($name, $owner));
    }

    /**
     * Get from cache.
     *
     * @return mixed|null if nothing was found in cache
     */
    protected function fetchFromCache(?string $name, ?SettingsOwnerInterface $owner = null)
    {
        $cacheKey = $this->getCacheKey($name, $owner);

        return $this->storage->getItem($cacheKey)->get();
    }

    /**
     * Store in cache.
     *
     * @param SettingsOwnerInterface $owner
     *
     * @return bool TRUE if the entry was successfully stored in the cache, FALSE otherwise
     */
    protected function storeInCache(?string $name, $value, ?SettingsOwnerInterface $owner = null): bool
    {
        $item = $this->storage->getItem($this->getCacheKey($name, $owner))
            ->set($value)
            ->expiresAfter($this->cacheLifeTime);

        return $this->storage->save($item);
    }

    /**
     * @param SettingsOwnerInterface $owner
     */
    protected function getCacheKey(?string $key, ?SettingsOwnerInterface $owner = null): string
    {
        return sprintf(self::PREFIX, $owner ? $owner->getSettingIdentifier() : '', $key);
    }
}
