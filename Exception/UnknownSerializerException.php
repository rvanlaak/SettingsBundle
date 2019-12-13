<?php

namespace Dmishh\SettingsBundle\Exception;

class UnknownSerializerException extends \RuntimeException implements SettingsException
{
    public function __construct($serializerClass)
    {
        parent::__construct(sprintf('Unknown serializer class "%s"', $serializerClass));
    }
}
