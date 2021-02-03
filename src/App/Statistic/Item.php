<?php

namespace App\Statistic;

class Item
{
    public int $customerId;

    // Number of customer's calls within same continent: (int) 2
    public int $sameContinentCallsTotalCount;

    // Total duration of customer's calls within same continent: (int) 191 of seconds
    public int $sameContinentCallsTotalDuration;

    // Number of all customer's calls: (int) 8
    public int $totalNumberOfAllCustomersCalls;

    public int $totalDurationOfAllCustomersCalls = 0;

    /**
     * @return int
     */
    public function getSameContinentCallsTotalCount(): int
    {
        return $this->sameContinentCallsTotalCount;
    }

    /**
     * @param int $sameContinentCallsTotalCount
     */
    public function setSameContinentCallsTotalCount(int $sameContinentCallsTotalCount): void
    {
        $this->sameContinentCallsTotalCount = $sameContinentCallsTotalCount;
    }

    /**
     * @return int
     */
    public function getSameContinentCallsTotalDuration(): int
    {
        return $this->sameContinentCallsTotalDuration;
    }

    /**
     * @param int $sameContinentCallsTotalDuration
     */
    public function setSameContinentCallsTotalDuration(int $sameContinentCallsTotalDuration): void
    {
        $this->sameContinentCallsTotalDuration = $sameContinentCallsTotalDuration;
    }

    /**
     * @return int
     */
    public function getTotalNumberOfAllCustomersCalls(): int
    {
        return $this->totalNumberOfAllCustomersCalls;
    }

    /**
     * @param int $totalNumberOfAllCustomersCalls
     */
    public function setTotalNumberOfAllCustomersCalls(int $totalNumberOfAllCustomersCalls): void
    {
        $this->totalNumberOfAllCustomersCalls = $totalNumberOfAllCustomersCalls;
    }

    /**
     * @return int
     */
    public function getTotalDurationOfAllCustomersCalls(): int
    {
        return $this->totalDurationOfAllCustomersCalls;
    }

    /**
     * @param int $totalDurationOfAllCustomersCalls
     */
    public function setTotalDurationOfAllCustomersCalls(int $totalDurationOfAllCustomersCalls): void
    {
        $this->totalDurationOfAllCustomersCalls = $totalDurationOfAllCustomersCalls;
    }
}
