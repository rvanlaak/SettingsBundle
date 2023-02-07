<?php

namespace Dmishh\SettingsBundle\Manager;

use Dmishh\SettingsBundle\Entity\Setting;
use Dmishh\SettingsBundle\Entity\SettingsOwnerInterface;
use Dmishh\SettingsBundle\Exception\UnknownSerializerException;
use Dmishh\SettingsBundle\Exception\UnknownSettingException;
use Dmishh\SettingsBundle\Exception\WrongScopeException;
use Dmishh\SettingsBundle\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/**
 * Settings Manager provides settings management and persistence using Doctrine's Object Manager.
 *
 * @author Dmitriy Scherbina <http://dmishh.com>
 * @author Artem Zhuravlov
 */
class SettingsManager implements SettingsManagerInterface
{
    /**
     * @var ?array<string, mixed>
     */
    private ?array $globalSettings = null;

    /**
     * @var ?array<string, array<string, mixed>>
     */
    private ?array $ownerSettings = null;

    /**
     * @var EntityRepository<Setting>
     */
    private EntityRepository $repository;

    public function __construct(
        private EntityManagerInterface $em,
        private SerializerInterface    $serializer,
        private array $settingsConfiguration = []
    ) {
        $this->repository = $em->getRepository(Setting::class);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $name, ?SettingsOwnerInterface $owner = null, mixed $default = null): mixed
    {
        $this->validateSetting($name, $owner);
        $this->loadSettings($owner);

        $value = null;

        switch ($this->settingsConfiguration[$name]['scope']) {
            case SettingsManagerInterface::SCOPE_GLOBAL:
                $value = $this->globalSettings[$name] ?? null;
                break;
            case SettingsManagerInterface::SCOPE_ALL:
                $value = $this->globalSettings[$name] ?? null;
            // Do not break here. Try to fetch the users settings
            // no break
            case SettingsManagerInterface::SCOPE_USER:
                if (null !== $owner) {
                    $value = $this->ownerSettings[$owner->getSettingIdentifier()][$name] ?? $value;
                }
                break;
        }

        return $value ?? $default;
    }

    /**
     * Returns all settings as associative name-value array.
     *
     * @return array<string, mixed>
     */
    public function all(?SettingsOwnerInterface $owner = null): array
    {
        $this->loadSettings($owner);

        if (null === $owner) {
            return $this->globalSettings;
        }

        $settings = $this->ownerSettings[$owner->getSettingIdentifier()];

        // If some user setting is not defined, please use the value from global
        foreach ($settings as $key => $value) {
            if (null === $value && isset($this->globalSettings[$key])) {
                $settings[$key] = $this->globalSettings[$key];
            }
        }

        return $settings;
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $name, mixed $value, ?SettingsOwnerInterface $owner = null): void
    {
        $this->setWithoutFlush($name, $value, $owner);
        $this->flush([$name], $owner);
    }

    /**
     * {@inheritdoc}
     */
    public function setMany(array $settings, ?SettingsOwnerInterface $owner = null): void
    {
        foreach ($settings as $name => $value) {
            $this->setWithoutFlush($name, $value, $owner);
        }

        $this->flush(array_keys($settings), $owner);
    }

    /**
     * {@inheritdoc}
     */
    public function clear(string $name, ?SettingsOwnerInterface $owner = null): void
    {
        $this->set($name, null, $owner);
    }

    /**
     * Find a setting by name form an array of settings.
     *
     * @param Setting[] $haystack
     */
    protected function findSettingByName(array $haystack, string $needle): ?Setting
    {
        foreach ($haystack as $setting) {
            if ($setting->getName() === $needle) {
                return $setting;
            }
        }

        return null;
    }

    /**
     * Sets setting value to private array. Used for settings' batch saving.
     */
    private function setWithoutFlush(string $name, mixed $value, ?SettingsOwnerInterface $owner = null): void
    {
        $this->validateSetting($name, $owner);
        $this->loadSettings($owner);

        if (null === $owner) {
            $this->globalSettings[$name] = $value;
        } else {
            $this->ownerSettings[$owner->getSettingIdentifier()][$name] = $value;
        }
    }

    /**
     * Flushes settings defined by $names to database.
     *
     * @param string[] $names
     *
     * @throws UnknownSerializerException
     */
    private function flush(array $names, ?SettingsOwnerInterface $owner = null): void
    {
        $settings = $this->repository->findBy([
            'name' => $names,
            'ownerId' => $owner?->getSettingIdentifier(),
        ]);

        // Assert: $settings might be a smaller set than $names

        // For each setting that you are trying to save
        foreach ($names as $name) {
            try {
                $value = $this->get($name, $owner);
            } catch (WrongScopeException) {
                continue;
            }

            $setting = $this->findSettingByName($settings, $name);

            if (null === $setting) {
                // if the setting does not exist in DB, create it
                $setting = new Setting();
                $setting->setName($name);
                if (null !== $owner) {
                    $setting->setOwnerId($owner->getSettingIdentifier());
                }
                $this->em->persist($setting);
            }

            $setting->setValue($this->serializer->serialize($value));
        }

        $this->em->flush();
    }

    /**
     * Checks that $name is valid setting and it's scope is also valid.
     *
     * @throws UnknownSettingException
     * @throws WrongScopeException
     */
    private function validateSetting(string $name, ?SettingsOwnerInterface $owner = null): void
    {
        // Name validation
        if (!\array_key_exists($name, $this->settingsConfiguration)) {
            throw new UnknownSettingException($name);
        }

        // Scope validation
        $scope = $this->settingsConfiguration[$name]['scope'];
        if (SettingsManagerInterface::SCOPE_ALL !== $scope) {
            if (SettingsManagerInterface::SCOPE_GLOBAL === $scope && null !== $owner || SettingsManagerInterface::SCOPE_USER === $scope && null === $owner) {
                throw new WrongScopeException($scope, $name);
            }
        }
    }

    /**
     * Settings lazy loading.
     */
    private function loadSettings(SettingsOwnerInterface $owner = null): void
    {
        // Global settings
        if (null === $this->globalSettings) {
            $this->globalSettings = $this->getSettingsFromRepository();
        }

        // User settings
        if (null !== $owner && (null === $this->ownerSettings || !\array_key_exists($owner->getSettingIdentifier(), $this->ownerSettings))) {
            $this->ownerSettings[$owner->getSettingIdentifier()] = $this->getSettingsFromRepository($owner);
        }
    }

    /**
     * Retrieves settings from repository.
     *
     * @return array<string, mixed>
     *
     * @throws UnknownSerializerException
     */
    private function getSettingsFromRepository(?SettingsOwnerInterface $owner = null): array
    {
        $settings = [];

        foreach (array_keys($this->settingsConfiguration) as $name) {
            try {
                $this->validateSetting($name, $owner);
                $settings[$name] = null;
            } catch (WrongScopeException) {
                continue;
            }
        }

        /** @var Setting $setting */
        foreach ($this->repository->findBy(['ownerId' => $owner?->getSettingIdentifier()]) as $setting) {
            if (\array_key_exists($setting->getName(), $settings)) {
                $settings[$setting->getName()] = $this->serializer->unserialize($setting->getValue());
            }
        }

        return $settings;
    }
}
