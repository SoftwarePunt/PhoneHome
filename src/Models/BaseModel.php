<?php

namespace SoftwarePunt\PhoneHome\Models;

abstract class BaseModel
{
    public function __construct(?array $data = null)
    {
        if ($data)
            $this->fillFromArray($data);
    }

    public function fillFromArray(array $data): void
    {
        $rfClass = new \ReflectionClass($this);

        foreach ($data as $key => $value) {
            if (!property_exists($this, $key))
                continue;

            $rfProp = $rfClass->getProperty($key);

            if (!$rfProp->isPublic())
                continue;

            $rfType = $rfProp->getType();
            $rfTypeName = $rfType->getName();

            if (is_array($value) && class_exists($rfTypeName) && is_subclass_of($rfTypeName, BaseModel::class)) {
                // Sub-model assignment by array
                $modelInstance = new $rfTypeName();
                $modelInstance->fillFromArray($value);
                $rfProp->setValue($this, $modelInstance);
            } else {
                // General assignment (may throw if incompatible types)
                $rfProp->setValue($this, $value);
            }
        }
    }
}