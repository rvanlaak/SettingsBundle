<?php
/**
 * Created by PhpStorm.
 * User: Victor
 * Date: 22/10/15
 * Time: 12:58
 */
namespace Dmishh\Bundle\SettingsBundle\Manager\MongoDB;
use Dmishh\Bundle\SettingsBundle\Document\Setting;
use Dmishh\Bundle\SettingsBundle\Entity\SettingsOwnerInterface;
use Dmishh\Bundle\SettingsBundle\Exception\WrongScopeException;
use Dmishh\Bundle\SettingsBundle\Serializer\SerializerInterface;
use Doctrine\ODM\MongoDB\DocumentManager;

class SettingsManager extends \Dmishh\Bundle\SettingsBundle\Manager\SettingsManager {

    /**
     * @param DocumentManager       $em
     * @param SerializerInterface $serializer
     * @param array               $settingsConfiguration
     */
    public function __construct(
        DocumentManager $em,
        SerializerInterface $serializer,
        array $settingsConfiguration = array()
    ) {
        $this->em = $em;
        $this->repository = $em->getRepository('Dmishh\Bundle\SettingsBundle\Document\Setting');
        $this->serializer = $serializer;
        $this->settingsConfiguration = $settingsConfiguration;
    }

    /**
     * Retreives settings from repository.
     *
     * @param SettingsOwnerInterface|null $owner
     *
     * @throws \Dmishh\Bundle\SettingsBundle\Exception\UnknownSerializerException
     * @return array
     */
    private function getSettingsFromRepository(SettingsOwnerInterface $owner = null)
    {
        $settings = array();

        foreach (array_keys($this->settingsConfiguration) as $name) {
            try {
                $this->validateSetting($name, $owner);
                $settings[$name] = null;
            } catch (WrongScopeException $e) {
                continue;
            }
        }

        /** @var Setting $setting */
        foreach ($this->repository->findBy(
            array('owner.id' => $owner === null ? null : $owner->getSettingIdentifier())
        ) as $setting) {
            if (array_key_exists($setting->getName(), $settings)) {
                $settings[$setting->getName()] = $this->serializer->unserialize($setting->getValue());
            }
        }

        return $settings;
    }

    protected function getNewSetting(SettingsOwnerInterface $owner = null){
        $setting = new Setting();
        if ($owner !== null) {
            $setting->setOwner($owner);
        }
        return $setting;

    }
}