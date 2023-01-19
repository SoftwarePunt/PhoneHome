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
            'os_banner' => self::getOsBanner(),
            'php_version' => phpversion(),
            'cwd' => getcwd()
        ];
    }

    private static function getOsBanner(): string
    {
        if (PHP_OS !== "Linux")
            return PHP_OS;

        $releaseInfo = self::getLinuxReleaseInfo();
        $parts = [
            ucfirst($releaseInfo['DISTRIB_ID'] ?? $releaseInfo['NAME'] ?? $releaseInfo['ID'] ?? "Unknown"),
            $releaseInfo['VERSION'] ?? $releaseInfo['VERSION_ID'] ?? "(Unknown Version)",
            "(Linux " . php_uname('rm') . ")"
        ];
        return implode(' ', $parts);
    }

    private static function getLinuxReleaseInfo(): array
    {
        $data = [];
        $files = glob('/etc/*-release');

        foreach ($files as $file) {
            $lines = array_filter(array_map(function ($line) {
                $parts = explode('=', $line);

                if (count($parts) !== 2)
                    return false;

                $parts[1] = str_replace(array('"', "'"), '', $parts[1]);
                return $parts;
            }, file($file)));

            foreach ($lines as $line)
                $data[$line[0]] = trim($line[1]);
        }

        return $data;
    }
}