<?php

namespace SoftwarePunt\PhoneHome;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use SoftwarePunt\PhoneHome\Models\PhoneHomeResponse;
use SoftwarePunt\PhoneHome\Providers\EnvironmentInfo;
use SoftwarePunt\PhoneHome\Providers\GitVersionInfo;
use SoftwarePunt\PhoneHome\Providers\NetworkInfo;
use SoftwarePunt\PhoneHome\Providers\SoftwareInfo;

class PhoneHome
{
    public const DefaultApiBaseUrl = "https://portal.softwarepunt.nl/api";
    public const DefaultTimeout = 10;

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
            'git' => (new GitVersionInfo()),
            'software' => (new SoftwareInfo())
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
     * Sends a ping to the configured API endpoint, and returns the server response, if any.
     *
     * @throws GuzzleException In case of request error.
     * @throws \RuntimeException In case of response error.
     * @return PhoneHomeResponse|null The parsed response from the server, or NULL if the response was empty or invalid.
     */
    public function send(): ?PhoneHomeResponse
    {
        $client = new Client([
            'base_uri' => $this->apiBaseUrl,
            'timeout' => $this->timeout
        ]);

        $response = $client->request('POST', '/api/etph', [
            'json' => $this->makeJsonPayload()
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException("Unexpected status code in response: {$response->getStatusCode()}");
        }

        return PhoneHomeResponse::tryParseFromStream($response->getBody());
    }
}