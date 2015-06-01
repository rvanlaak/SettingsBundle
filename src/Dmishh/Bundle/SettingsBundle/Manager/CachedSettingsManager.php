<?php

/**
 * This file is part of the DmishhSettingsBundle package.
 * (c) 2013 Dmitriy Scherbina <http://dmishh.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmishh\Bundle\SettingsBundle\Manager;

use Dmishh\Bundle\SettingsBundle\Entity\SettingsOwnerInterface;
use Doctrine\Common\Cache\CacheProvider;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

/**
 * @author Tobias Nyholm
 */
class CachedSettingsManager implements SettingsManagerInterface
{
    const PREFIX = 'dmishh_settings_o{%s}_k{%s}';

    /**
     * @var CacheProvider $storage
     */
    private $storage;

    /**
     * @var SettingsManagerInterface $settingsManagers
     */
    private $settingsManager;

    /**
     * @var int $cacheLifeTime
     */
    private $cacheLifeTime;

    /**
     * @param SettingsManagerInterface $settingsManager
     */
    public function __construct(SettingsManagerInterface $settingsManager, $cacheLifeTime)
    {
        $this->settingsManager = $settingsManager;
        $this->cacheLifeTime = $cacheLifeTime;
    }

    /**
     * @param CacheProvider $storage
     *
     * @return $this
     */
    public function setCacheStorage(CacheProvider $storage)
    {
        $this->storage = $storage;

        return $this;
    }

    /**
     * Get the storage.
     *
     * @return CacheProvider
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     */
    protected function getCacheStorage()
    {
        if ($this->storage === null) {
            throw new ServiceNotFoundException('Could not find a cache service');
        }

        return $this->storage;
    }

    /**
     * {@inheritdoc}
     */
    public function get($name, SettingsOwnerInterface $owner = null, $default = null)
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
    public function all(SettingsOwnerInterface $owner = null)
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
    public function set($name, $value, SettingsOwnerInterface $owner = null)
    {
        $this->invalidateCache($name, $owner);

        return $this->settingsManager->set($name, $value, $owner);
    }

    /**
     * {@inheritdoc}
     */
    public function setMany(array $settings, SettingsOwnerInterface $owner = null)
    {
        foreach ($settings as $key => $value) {
            $this->invalidateCache($key, $owner);
        }

        return $this->settingsManager->setMany($settings, $owner);
    }

    /**
     * {@inheritdoc}
     */
    public function clear($name, SettingsOwnerInterface $owner = null)
    {
        $this->invalidateCache($name, $owner);

        return $this->settingsManager->clear($name, $owner);
    }

    /**
     * @param SettingsOwnerInterface $owner
     * @param string                 $name
     *
     * @return boolean TRUE if the cache entry was successfully deleted, FALSE otherwise.
     */
    protected function invalidateCache($name, SettingsOwnerInterface $owner = null)
    {
        return $this->getCacheStorage()->delete($this->getCacheKey($name, $owner));
    }

    /**
     * Get from cache.
     *
     * @param SettingsOwnerInterface $owner
     * @param string                 $name
     *
     * @return mixed|null if nothing was found in cache
     */
    protected function fetchFromCache($name, SettingsOwnerInterface $owner = null)
    {
        $storage = $this->getCacheStorage();
        $cacheKey = $this->getCacheKey($name, $owner);

        if (!$storage->contains($cacheKey)) {
            return;
        }

        return $storage->fetch($cacheKey);
    }

    /**
     * Store in cache.
     *
     * @param SettingsOwnerInterface $owner
     * @param string                 $name
     * @param mixed                  $value
     *
     * @return boolean TRUE if the entry was successfully stored in the cache, FALSE otherwise.
     */
    protected function storeInCache($name, $value, SettingsOwnerInterface $owner = null)
    {
        return $this->getCacheStorage()->save($this->getCacheKey($name, $owner), $value, $this->cacheLifeTime);
    }

    /**
     * @param string                 $key
     * @param SettingsOwnerInterface $owner
     *
     * @return string
     */
    protected function getCacheKey($key, SettingsOwnerInterface $owner = null)
    {
        return sprintf(self::PREFIX, $owner ? $owner->getSettingIdentifier() : '', $key);
    }
}
