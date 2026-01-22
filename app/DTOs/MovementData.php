<?php

namespace App\DTOs;

class MovementData
{
    public function __construct(
        public ?int $id = null,
        public int $id_refer,
        public int $id_article,
        public int $quantity,
        public ?bool $delete = false
    ) {}
}