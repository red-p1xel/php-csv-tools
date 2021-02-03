<?php

namespace App\Statistic;

class StatsList
{
    /**
     * @var Item[] StatisticItems
     */
    private $items;

    /**
     * @param mixed ...$item
     */
    public function __construct(Item ...$item)
    {
        $this->items = $item;
    }

    /**
     * @param Item $item
     */
    public function add(Item $item): void
    {
        $this->items[] = $item;
    }

    /**
     * @return Item[] Calls
     */
    public function all(): array
    {
        return $this->items;
    }
}
