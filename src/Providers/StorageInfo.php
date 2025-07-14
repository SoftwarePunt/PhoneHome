<?php

namespace SoftwarePunt\PhoneHome\Providers;

class StorageInfo implements \JsonSerializable
{
    public function __construct()
    {
    }

    public function jsonSerialize(): mixed
    {
        return $this->getStorageDeviceUsages();
    }

    private static function getStorageDeviceUsages(): array
    {
        $devices = [];
        $mounts = file_get_contents('/proc/mounts');
        $mounts = explode("\n", $mounts);

        foreach ($mounts as $mount) {
            try {
                $parts = explode(' ', $mount);
                $device = $parts[0];

                if (!str_starts_with($device, '/dev/'))
                    continue;

                $mountPoint = $parts[1];
                $fileSystemType = $parts[2];
                $mountOptions = $parts[3];

                $total = disk_total_space($mountPoint);
                $free = disk_free_space($mountPoint);
                $used = $total - $free;
                $percent = round($used / $total * 100, 2);

                $devices[] = [
                    'device' => $device,
                    'filesystem' => $fileSystemType,
                    'options' => $mountOptions,
                    'mount' => $mountPoint,
                    'total' => $total,
                    'free' => $free,
                    'used' => $used,
                    'percent' => $percent
                ];
            } catch (\Exception) {
            }
        }

        return $devices;
    }
}