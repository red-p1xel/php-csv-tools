<?php

namespace App\Model;

class StatRepo implements EntityRepository
{
    /**
     * @var StatItem[] StatisticItems
     */
    private $items;

    /**
     * @param mixed ...$item
     */
    public function __construct(StatItem ...$item)
    {
        $this->items = $item;
    }

    /**
     * @param StatItem ...$item
     */
    public function add(StatItem ...$item): void
    {
        $this->items[] = $item;
    }

    /**
     * @return StatItem[] Items
     */
    public function all(): array
    {
        return $this->items;
    }
}
