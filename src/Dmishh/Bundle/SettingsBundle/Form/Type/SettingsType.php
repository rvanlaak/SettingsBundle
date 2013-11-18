<?php

/**
 * This file is part of the DmishhSettingsBundle package.
 *
 * (c) 2013 Dmitriy Scherbina <http://dmishh.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmishh\Bundle\SettingsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SettingsType extends AbstractType
{
    protected $settingsConfiguration;

    public function __construct(array $settingsConfiguration)
    {
        $this->settingsConfiguration = $settingsConfiguration;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($this->settingsConfiguration as $name => $configuration) {
            if (array_key_exists($name, $options['data']) && !in_array($name, $options['disabled_settings'])) {
                $builder->add($name, 'dmishh_setting', array('configuration' => $configuration['validation']));
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'disabled_settings' => array(),
            )
        );

        $resolver->addAllowedTypes(
            array(
                'disabled_settings' => 'array'
            )
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'dmishh_settings';
    }
}
