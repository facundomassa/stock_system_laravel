<?php

namespace App\DTOs;

class ReferStatusData
{
    public function __construct(
        public int $referId,
        public string $status,
        public ?string $dateEnded = null,
        public ?string $observation = null
    ) {}
}