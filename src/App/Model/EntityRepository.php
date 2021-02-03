<?php

namespace App\Model;

/**
 * @package App\Model
 */
interface EntityRepository
{
    public function all(): array;
}
