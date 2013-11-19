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
        foreach ($this->settingsConfiguration as $name => $config) {
            if (array_key_exists($name, $options['data']) && !in_array($name, $options['disabled_settings'])) {

                if (!empty($config['validation']['options']['choices'])) {
                    $config['validation']['options']['choices'] = array_combine($config['validation']['options']['choices'], $config['validation']['options']['choices']);
                }

                $builder->add($name, $config['validation']['type'], $config['validation']['options']);
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
