<?php

namespace SoftwarePunt\PhoneHome\Status;

/**
 * Result enum for status monitor checks. Indicates the outcome of a status check.
 *
 * Each code falls into one of three categories:
 *  - Success (`getIsSuccess()`): The system is functioning as expected.
 *  - Failure (`getIsFailure()`): The system is not functioning as expected or its status could not be determined.
 *  - Inconclusive (`getIsInconclusive()`): The status check was skipped or not applicable.
 */
enum StatusMonitorCode: string
{
    /**
     * Indicates that the status check was performed successfully and the monitored system is functioning as expected.
     *
     * Example: system is online, performed its tasks on time, and returned the expected results.
     */
    case HEALTHY = 'healthy';
    /**
     * Indicates that the status check was performed successfully, but the monitored system is not functioning as expected.
     *
     * Example: system is online but not performing its tasks on time, or returned unexpected results.
     */
    case UNHEALTHY = 'unhealthy';
    /**
     * Indicates that the status check failed due to an error, such as an exception or a timeout.
     *
     * Example: system is offline or failing, and no status check could be performed.
     */
    case ERROR = 'error';
    /**
     * Indicates that the status check was skipped, either because it was not applicable or due to a configuration issue.
     *
     * Example: system is not configured to perform this check, or the check was intentionally skipped.
     */
    case SKIPPED = 'skipped';

    /**
     * Gets whether the status check result is considered a pass (i.e., healthy).
     */
    public function getIsSuccess(): bool
    {
        return match ($this) {
            self::HEALTHY => true,
            default => false,
        };
    }

    /**
     * Gets whether the status check result is considered a failure (i.e., unhealthy or error).
     */
    public function getIsFailure(): bool
    {
        return match ($this) {
            self::UNHEALTHY, self::ERROR => true,
            default => false,
        };
    }

    /**
     * Gets whether the status check result is inconclusive (i.e., skipped).
     */
    public function getIsInconclusive(): bool
    {
        return match ($this) {
            self::SKIPPED => true,
            default => false,
        };
    }

    /**
     * Gets the standard color code descriptor for the status check result.
     *
     * @return "green"|"red"|"gray"
     */
    public function getColor(): string
    {
        if ($this->getIsSuccess()) {
            return 'green';
        } elseif ($this->getIsFailure()) {
            return 'red';
        } else {
            return 'gray';
        }
    }
}
