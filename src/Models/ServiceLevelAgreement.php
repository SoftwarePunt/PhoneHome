<?php

namespace SoftwarePunt\PhoneHome\Models;

class ServiceLevelAgreement extends BaseModel
{
    public bool $active = false;
    public ?string $reference = null;
    public array $contactMethods = [];
}