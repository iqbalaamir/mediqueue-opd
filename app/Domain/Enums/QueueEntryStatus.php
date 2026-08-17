<?php

namespace App\Domain\Enums;

enum QueueEntryStatus: string
{
    case Waiting = 'waiting';
    case Called = 'called';
    case Serving = 'serving';
    case Completed = 'completed';
    case Skipped = 'skipped';

    public function label(): string
    {
        return match ($this) {
            self::Waiting => 'Waiting',
            self::Called => 'Called',
            self::Serving => 'Serving',
            self::Completed => 'Completed',
            self::Skipped => 'Skipped',
        };
    }
}
