<?php

namespace SoftwarePunt\PhoneHome\Status;

abstract class BaseStatusMonitor
{
    public function __construct(
        public readonly string $id,
        public readonly string $description
    )
    {
    }

    /**
     * Performs the status monitor check and returns the result.
     *
     * @throws \Throwable If an unexpected error occurs during the check; must be caught and treated as ERROR result.
     */
    public abstract function performCheck(): StatusMonitorResult;
}