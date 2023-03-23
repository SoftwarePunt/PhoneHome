<?php

namespace SoftwarePunt\PhoneHome\InfoProviders;

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
            'nginx' => self::getShellResult('nginx -v', prefixRemove: 'nginx version:'),
            'mysql' => self::getShellResult('mysql --version'),
            'redis' => self::getShellResult('redis-server --version')
        ];
    }

    private static function getShellResult(string $shellCommand, ?string $prefixRemove = ""): ?string
    {
        $result = @shell_exec($shellCommand . ' 2>&1');

        if (str_contains($result, ": not found"))
            return null;

        if ($prefixRemove)
            $result = str_replace($prefixRemove, '', $result);

        return trim($result);
    }

    public function jsonSerialize(): mixed
    {
        return $this->versionInfos;
    }
}