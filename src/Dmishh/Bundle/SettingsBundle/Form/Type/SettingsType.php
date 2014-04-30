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
use Symfony\Component\Validator\Constraint;

/**
 * Settings management form
 *
 * @author Dmitriy Scherbina <http://dmishh.com>
 */
class SettingsType extends AbstractType
{
    protected $settingsConfiguration;

    public function __construct( array $settingsConfiguration )
    {
        $this->settingsConfiguration = $settingsConfiguration;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        foreach ( $this->settingsConfiguration as $name => $configuration ) {
            // If setting's value exists in data and setting isn't disabled
            if ( array_key_exists( $name, $options[ 'data' ] ) && !in_array( $name, $options[ 'disabled_settings' ] ) ) {
                $fieldType = $configuration[ 'validation' ][ 'type' ];
                $fieldOptions = $configuration[ 'validation' ][ 'options' ];

                // Validator Constraints
                $buffer = [ ];
                if ( array_key_exists( 'constraints', $fieldOptions ) ) {
                    foreach ( $fieldOptions[ 'constraints' ] as $class => $opt ) {
                        $buffer[ ] = new $class( $opt );
                    }
                    $fieldOptions[ 'constraints' ] = $buffer;
                }

                // Label I18n
                $fieldOptions[ 'label' ] = 'labels.' . $name;
                $fieldOptions[ 'translation_domain' ] = 'settings';

                // Choices I18n
                if ( !empty( $fieldOptions[ 'choices' ] ) ) {
                    $fieldOptions[ 'choices' ] = array_map(
                        function ( $label ) use ( $fieldOptions ) {
                            return $fieldOptions[ 'label' ] . '_choices.' . $label;
                        },
                        array_combine( $fieldOptions[ 'choices' ], $fieldOptions[ 'choices' ] )
                    );
                }
                $builder->add( $name, $fieldType, $fieldOptions );
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions( OptionsResolverInterface $resolver )
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
        return 'settings_management';
    }
}
