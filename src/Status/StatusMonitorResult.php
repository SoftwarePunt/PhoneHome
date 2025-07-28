<?php

namespace SoftwarePunt\PhoneHome\Status;

readonly class StatusMonitorResult
{
    public function __construct(
        public StatusMonitorCode $code,
        public ?string           $message = null,
        public ?\Throwable       $exception = null
    )
    {
    }
}