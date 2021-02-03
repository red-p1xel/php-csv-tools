<?php

namespace App\Model;

use App\Model\Call;

/**
 * Class CallRepo
 * @package App\Model
 */
class CallRepo implements EntityRepository
{
    /**
     * @var Call[] Calls
     */
    public $calls;

    /**
     * RecordList constructor.
     * @param mixed ...$call
     */
    public function __construct(Call ...$call)
    {
        $this->calls = $call;
    }

    /**
     * @param Call $call
     */
    public function add(Call $call): void
    {
        $this->calls[] = $call;
    }

    /**
     * @param $key
     * @param $value
     * @return Call[] Calls
     */
    public function get($key, $value): array
    {
        $collection = [];

        foreach ($this->calls as $call => $val) {
            if ($val->{$key} == $value) {
                $collection[] = $val;
            } else {
                break;
            }
            continue;
        }

        return $collection;
    }
    /**
     * @return Call[] Calls
     */
    public function all(): array
    {
        return $this->calls;
    }

    /**
     * @return array
     */
    public function unique(): array
    {
        return array_unique($this->calls);
    }

    /**
     * Count total calls and total calls durations getting by key field and value
     *
     * @param array $calls
     * @return int
     */
    public function totalCallsDurationsBy(array $calls)
    {
        $totalDuration = 0;
        /** @var Call $list */
        $list = $calls;

        foreach ($list as $call => $val) {
            $totalDuration = $totalDuration + intval($val->duration);
        }

        return $totalDuration;
    }
}
