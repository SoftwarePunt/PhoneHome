<?php

namespace SoftwarePunt\PhoneHome\Providers;

class NetworkInfo implements \JsonSerializable
{
    private ?string $localAddr;
    private ?string $wanAddrIpv4;
    private ?string $wanAddrIpv6;

    public function __construct()
    {
        $this->tryDetermineLocalAddr();
        $this->tryDetermineWanAddr();
    }

    private function tryDetermineLocalAddr(): void
    {
        $this->localAddr = null;

        if (extension_loaded('sockets')) {
            try {
                $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
                socket_connect($sock, "8.8.8.8", 53);
                socket_getsockname($sock, $name); // $name passed by reference
                $this->localAddr = $name;
            } catch (\Exception) {
            }
        }
    }

    private function tryDetermineWanAddr(): void
    {
        try {
            $this->wanAddrIpv4 = @file_get_contents('https://ipv4.icanhazip.com/');
        } catch (\Exception) {
            $this->wanAddrIpv4 = null;
        }

        try {
            $this->wanAddrIpv6 = @file_get_contents('https://ipv6.icanhazip.com/');
        } catch (\Exception) {
            $this->wanAddrIpv6 = null;
        }

        if (!empty($this->wanAddrIpv4)) {
            $this->wanAddrIpv4 = trim($this->wanAddrIpv4);
        } else {
            $this->wanAddrIpv4 = null;
        }

        if (!empty($this->wanAddrIpv6)) {
            $this->wanAddrIpv6 = trim($this->wanAddrIpv6);
        } else {
            $this->wanAddrIpv6 = null;
        }
    }

    public function jsonSerialize(): mixed
    {
        return [
            'local_addr' => $this->localAddr,
            'wan_addr' => $this->wanAddrIpv4,
            'wan_addr_ipv6' => $this->wanAddrIpv6
        ];
    }
}