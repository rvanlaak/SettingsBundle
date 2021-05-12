<?php


namespace Dmishh\SettingsBundle\Exception;


class UnknownConstraintClassException extends \RuntimeException implements SettingsException
{
    public function __construct($constraintClass)
    {
        parent::__construct(sprintf('Constraint class "%s" not found', $constraintClass));
    }
}
