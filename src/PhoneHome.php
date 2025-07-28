<?php

namespace SoftwarePunt\PhoneHome;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use SoftwarePunt\PhoneHome\Models\PhoneHomeResponse;
use SoftwarePunt\PhoneHome\Providers\EnvironmentInfo;
use SoftwarePunt\PhoneHome\Providers\GitVersionInfo;
use SoftwarePunt\PhoneHome\Providers\NetworkInfo;
use SoftwarePunt\PhoneHome\Providers\SoftwareInfo;
use SoftwarePunt\PhoneHome\Providers\StorageInfo;
use SoftwarePunt\PhoneHome\Status\BaseStatusMonitor;
use SoftwarePunt\PhoneHome\Status\StatusMonitorCode;
use SoftwarePunt\PhoneHome\Status\StatusMonitorResult;

final class PhoneHome
{
    public const string API_BASE_URL_DEFAULT = "https://portal.softwarepunt.nl/api";
    public const float TIMEOUT_SECONDS_DEFAULT = 10.0;

    // -----------------------------------------------------------------------------------------------------------------
    // Common

    /**
     * @var BaseStatusMonitor[]
     */
    private array $statusMonitors;

    public function __construct(private string $apiBaseUrl = self::API_BASE_URL_DEFAULT,
                                private string $token = "",
                                private int    $timeout = self::TIMEOUT_SECONDS_DEFAULT)
    {
        $this->setApiBaseUrl($apiBaseUrl);
        $this->setToken($token);
        $this->setTimeout($timeout);
        $this->statusMonitors = [];
    }

    public function makeJsonPayload(): array
    {
        return [
            'token' => $this->token,
            'time' => time(),
            'environment' => (new EnvironmentInfo()),
            'network' => (new NetworkInfo()),
            'git' => (new GitVersionInfo()),
            'software' => (new SoftwareInfo()),
            'storage' => (new StorageInfo()),
            'statuses' => $this->performStatusMonitors()
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
    // Status monitoring

    public function addStatusMonitor(BaseStatusMonitor $statusMonitor): self
    {
        $this->statusMonitors[] = $statusMonitor;
        return $this;
    }

    public function performStatusMonitors(): array
    {
        $results = [];

        foreach ($this->statusMonitors as $monitor) {
            try {
                $result = $monitor->performCheck();
            } catch (\Throwable $ex) {
                $result = new StatusMonitorResult(
                    code: StatusMonitorCode::ERROR,
                    message: "Unexpected exception in monitor check",
                    exception: $ex
                );
            }

            $results[$monitor->id] = [
                'id' => $monitor->id,
                'description' => $monitor->description,
                'code' => $result->code->value,
                'message' => $result->message,
                'exception' => $result->exception ? [
                    'class' => get_class($result->exception),
                    'message' => $result->exception->getMessage(),
                    'trace' => $result->exception->getTraceAsString()
                ] : null,
                'color' => $result->code->getColor()
            ];
        }

        return $results;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Action

    /**
     * ET will home phone when you call this.
     *
     * Sends a ping to the configured API endpoint, and returns the server response, if any.
     *
     * @return PhoneHomeResponse|null The parsed response from the server, or NULL if the response was empty or invalid.
     * @throws \RuntimeException In case of response error.
     * @throws GuzzleException In case of request error.
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