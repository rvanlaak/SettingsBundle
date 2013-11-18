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

class SettingType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!empty($options['configuration']['options']['choices'])) {
            $options['configuration']['options']['choices'] = array_combine($options['configuration']['options']['choices'], $options['configuration']['options']['choices']);
        }

        $builder->add(
            'value',
            $options['configuration']['type'],
            $options['configuration']['options']
        );
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'configuration' => array(),
            )
        );

        $resolver->addAllowedTypes(
            array(
                'configuration' => 'array'
            )
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'dmishh_setting';
    }
}
