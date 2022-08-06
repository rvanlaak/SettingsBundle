<?php

namespace Dmishh\SettingsBundle\Exception;

class UnknownSerializerException extends \RuntimeException implements SettingsException
{
    public function __construct(string $serializerClass)
    {
        parent::__construct(sprintf('Unknown serializer class "%s"', $serializerClass));
    }
}
