<?php

namespace SoftwarePunt\PhoneHome;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use SoftwarePunt\PhoneHome\InfoProviders\EnvironmentInfo;
use SoftwarePunt\PhoneHome\InfoProviders\GitVersionInfo;
use SoftwarePunt\PhoneHome\InfoProviders\NetworkInfo;

class PhoneHome
{
    private const DefaultApiBaseUrl = "https://portal.softwarepunt.nl/api";
    private const DefaultTimeout = 10;

    // -----------------------------------------------------------------------------------------------------------------
    // Common

    private string $apiBaseUrl;
    private string $token;
    private int $timeout;

    public function __construct()
    {
        $this->setApiBaseUrl(self::DefaultApiBaseUrl);
        $this->setToken("");
        $this->setTimeout(self::DefaultTimeout);
    }

    public function makeJsonPayload(): array
    {
        return [
            'token' => $this->token,
            'time' => time(),
            'environment' => (new EnvironmentInfo()),
            'network' => (new NetworkInfo()),
            'git' => (new GitVersionInfo())
        ];
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Config

    public function setApiBaseUrl(string $apiBaseUrl): self
    {
        $this->apiBaseUrl = $apiBaseUrl;
        return $this;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;
        return $this;
    }

    public function setTimeout(float $timeout): self
    {
        $this->timeout = $timeout;
        return $this;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Action

    /**
     * ET will home phone when you call this.
     *
     * @throws GuzzleException
     * @throws \RuntimeException
     */
    public function send(): void
    {
        $client = new Client([
            'base_uri' => $this->apiBaseUrl,
            'timeout' => $this->timeout
        ]);

        echo json_encode($this->makeJsonPayload()) . PHP_EOL;

        $response = $client->request('POST', '/api/etph', [
            'json' => $this->makeJsonPayload()
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException("Unexpected status code in response: {$response->getStatusCode()}");
        }

        if ($response->getBody()->getContents() !== "👍") {
            throw new \RuntimeException("Invalid response body from server");
        }
    }
}