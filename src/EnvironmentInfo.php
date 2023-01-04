<?php

namespace SoftwarePunt\PhoneHome;

class EnvironmentInfo implements \JsonSerializable
{
    private array $serverGlobals;

    public function __construct(array $serverGlobals)
    {
        $this->serverGlobals = $serverGlobals;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'hostname' => gethostname(),
            'os' => PHP_OS,
            'os_uname' => php_uname(),
            'php_version' => phpversion(),
            'sapi_name' => php_sapi_name(),
            'cgi_version' => $this->serverGlobals['GATEWAY_INTERFACE'] ?? null,
            'server_name' => $this->serverGlobals['SERVER_NAME'] ?? null,
            'server_software' => $this->serverGlobals['SERVER_SOFTWARE'] ?? null,
            'document_root' => $this->serverGlobals['DOCUMENT_ROOT'] ?? null,
            'server_signature' => $this->serverGlobals['SERVER_SIGNATURE'] ?? null,
        ];
    }
}