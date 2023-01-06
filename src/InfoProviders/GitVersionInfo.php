<?php

namespace SoftwarePunt\PhoneHome\InfoProviders;

class GitVersionInfo implements \JsonSerializable
{
    private array $gitInfo;

    public function __construct()
    {
        $this->tryGetGitVersion();
    }

    private function tryGetGitVersion()
    {
        $this->gitInfo = [];

        $logHeadHashResult = exec('git log --pretty="%h" -n1 HEAD');
        $logHeadDateResult = exec('git log -n1 --pretty=%ci HEAD');

        if ($logHeadHashResult) {
            $this->gitInfo['hash'] = trim($logHeadHashResult);
        }

        if ($logHeadDateResult) {
            try {
                $commitDate = new \DateTime(trim($logHeadDateResult));
                $commitDate->setTimezone(new \DateTimeZone('UTC'));
                $this->gitInfo['date'] = $commitDate;
            } catch (\Exception) { }
        }
    }

    public function jsonSerialize(): mixed
    {
        return $this->gitInfo;
    }
}