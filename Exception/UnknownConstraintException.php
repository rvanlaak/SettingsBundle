<?php

namespace Dmishh\SettingsBundle\Exception;

class UnknownConstraintException extends \RuntimeException implements SettingsException
{
    public function __construct(string $className)
    {
        parent::__construct(sprintf('Constraint class "%s" not found', $className));
    }
}
