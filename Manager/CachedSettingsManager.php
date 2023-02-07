<?php

namespace Dmishh\SettingsBundle\Manager;

use Dmishh\SettingsBundle\Entity\SettingsOwnerInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * @author Tobias Nyholm
 */
class CachedSettingsManager implements SettingsManagerInterface
{
    public const PREFIX = 'dmishh_settings_%s_%s';

    public function __construct(private SettingsManagerInterface $settingsManager,
                                private CacheItemPoolInterface $storage,
                                private int|\DateInterval|null $cacheLifeTime)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $name, ?SettingsOwnerInterface $owner = null, mixed $default = null): mixed
    {
        if (null !== $cached = $this->fetchFromCache($name, $owner)) {
            return $cached;
        }

        $value = $this->settingsManager->get($name, $owner, $default);
        $this->storeInCache($name, $value, $owner);

        return $value;
    }

    /**
     * Check cache and populate it if necessary.
     * Returns all settings as associative name-value array.
     *
     * @return array<string, mixed>
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
    public function set(string $name, mixed $value, ?SettingsOwnerInterface $owner = null): void
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
     * @return bool TRUE if the entry was successfully stored in the cache, FALSE otherwise
     */
    protected function storeInCache(?string $name, mixed $value, ?SettingsOwnerInterface $owner = null): bool
    {
        $item = $this->storage->getItem($this->getCacheKey($name, $owner))
            ->set($value)
            ->expiresAfter($this->cacheLifeTime);

        return $this->storage->save($item);
    }

    protected function getCacheKey(?string $key, ?SettingsOwnerInterface $owner = null): string
    {
        return sprintf(self::PREFIX, $owner ? $owner->getSettingIdentifier() : '', $key);
    }
}
