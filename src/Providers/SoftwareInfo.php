<?php

namespace SoftwarePunt\PhoneHome\Providers;

class SoftwareInfo implements \JsonSerializable
{
    private array $versionInfos;

    public function __construct()
    {
        $this->collectVersionInfo();
    }

    private function collectVersionInfo(): void
    {
        $this->versionInfos = [
            'nginx' => self::getShellResult('nginx -v', prefixRemove: 'nginx version:') ?? self::getShellResult('/usr/sbin/nginx -v', prefixRemove: 'nginx version:'),
            'mysql' => self::getShellResult('mysqld --version', prefixRemove: '/usr/sbin/') ?? self::getShellResult('mysql --version'),
            'redis' => self::getShellResult('redis-server --version'),
            'dotnet' => self::getShellResult('dotnet --version'),
            'python' => self::getShellResult('python --version') ?? self::getShellResult('python3 --version'),
            'nodejs' => self::getShellResult('node --version'),
            'composer' => self::getShellResult('COMPOSER_ALLOW_SUPERUSER=1 composer --version', onlyFirstLine: true),
            'openssl' => self::getShellResult('openssl version'),
        ];
    }

    private static function getShellResult(string $shellCommand, ?string $prefixRemove = "", bool $onlyFirstLine = false): ?string
    {
        $result = @shell_exec($shellCommand . ' 2>&1');

        if (str_contains($result, ": not found") || str_contains($result, "can be installed"))
            return null;

        if ($prefixRemove)
            $result = str_replace($prefixRemove, '', $result);

        if ($onlyFirstLine)
            $result = explode("\n", $result)[0];

        return trim($result);
    }

    public function jsonSerialize(): mixed
    {
        return $this->versionInfos;
    }
}