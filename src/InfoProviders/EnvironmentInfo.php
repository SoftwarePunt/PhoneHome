<?php

namespace SoftwarePunt\PhoneHome\InfoProviders;

class EnvironmentInfo implements \JsonSerializable
{
    public function __construct()
    {
    }

    public function jsonSerialize(): mixed
    {
        return [
            'hostname' => gethostname(),
            'os' => PHP_OS,
            'os_uname' => php_uname(),
            'php_version' => phpversion(),
            'cwd' => getcwd()
        ];
    }
}