<?php

namespace SoftwarePunt\PhoneHome\Status;

class FileStatusMonitor extends BaseStatusMonitor
{
    public function __construct(
        string $id,
        string $description,
        public readonly string $filePath,
        public ?\DateInterval $expectedMaxAge = null,
        public ?int $expectedMinSizeBytes = null
    ) {
        parent::__construct($id, $description);
    }

    public function expectMaxAge(?\DateInterval $expectedMaxAge): self
    {
        $this->expectedMaxAge = $expectedMaxAge;
        return $this;
    }

    public function expectMinSizeBytes(?int $expectedMinSizeBytes): self
    {
        $this->expectedMinSizeBytes = $expectedMinSizeBytes;
        return $this;
    }

    public function expectNotEmpty(): self
    {
        $this->expectedMinSizeBytes = 1;
        return $this;
    }

    #[\Override] public function performCheck(): StatusMonitorResult
    {
        if (!file_exists($this->filePath) || !is_readable($this->filePath)) {
            return new StatusMonitorResult(
                code: StatusMonitorCode::UNHEALTHY,
                message: "File does not exist or is not readable: {$this->filePath}"
            );
        }

        if ($this->expectedMaxAge) {
            $fileModifiedTime = filemtime($this->filePath);

            if ($fileModifiedTime === false) {
                return new StatusMonitorResult(
                    code: StatusMonitorCode::UNHEALTHY,
                    message: "Could not retrieve file modification time for: {$this->filePath}"
                );
            }

            $maxAge = new \DateTime('now');
            $maxAge->sub($this->expectedMaxAge);

            $diff = $maxAge->getTimestamp() - $fileModifiedTime;

            if ($diff > 0) {
                return new StatusMonitorResult(
                    code: StatusMonitorCode::UNHEALTHY,
                    message: "File is older than expected max age of {$this->expectedMaxAge->format('%H:%I:%S')}: {$this->filePath}"
                );
            }
        }

        if ($this->expectedMinSizeBytes) {
            $fileSize = filesize($this->filePath);

            if ($fileSize === false) {
                return new StatusMonitorResult(
                    code: StatusMonitorCode::UNHEALTHY,
                    message: "Could not retrieve file size for: {$this->filePath}"
                );
            }

            if ($fileSize < $this->expectedMinSizeBytes) {
                return new StatusMonitorResult(
                    code: StatusMonitorCode::UNHEALTHY,
                    message: "File size is less than expected minimum of {$this->expectedMinSizeBytes} bytes: {$this->filePath}"
                );
            }
        }

        return new StatusMonitorResult(
            code: StatusMonitorCode::HEALTHY
        );
    }
}