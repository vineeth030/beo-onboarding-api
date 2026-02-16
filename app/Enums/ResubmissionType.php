<?php

namespace App\Enums;

enum ResubmissionType: string
{
    case Document = 'document';
    case Education = 'education';
    case Employment = 'employment';
    case Profile = 'profile';

    public function getLabel(): string
    {
        return match ($this) {
            self::Document => 'document',
            self::Education => 'education details',
            self::Employment => 'employment details',
            self::Profile => 'profile details',
        };
    }

    public function getActivityType(): string
    {
        return "update.{$this->value}.resubmitted";
    }

    public function getReopenActivityType(): string
    {
        return "reopen.{$this->value}";
    }
}
