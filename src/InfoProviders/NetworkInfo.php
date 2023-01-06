<?php

namespace SoftwarePunt\PhoneHome\InfoProviders;

class NetworkInfo implements \JsonSerializable
{
    private string $localAddr;
    private string $wanAddr;

    public function __construct()
    {
        $this->tryDetermineLocalAddr();
        $this->tryDetermineWanAddr();
    }

    private function tryDetermineLocalAddr(): void
    {
        if (extension_loaded('sockets')) {
            $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
            socket_connect($sock, "8.8.8.8", 53);
            socket_getsockname($sock, $name); // $name passed by reference
            $this->localAddr = $name;
        } else {
            $this->localAddr = null;
        }
    }

    private function tryDetermineWanAddr(): void
    {
        $this->wanAddr = @file_get_contents("https://checkip.amazonaws.com/");
        if (!$this->wanAddr) {
            $this->wanAddr = @file_get_contents("https://icanhazip.com/");
        }
        if (!empty($this->wanAddr)) {
            $this->wanAddr = trim($this->wanAddr);
        } else {
            $this->wanAddr = null;
        }
    }

    public function jsonSerialize(): mixed
    {
        return [
            'local_addr' => $this->localAddr,
            'wan_addr' => $this->wanAddr
        ];
    }
}