<?php

namespace App\Model;

class StatItem
{
    public int $customerId;

    // Number of customer's calls within same continent: (int) 2
    public int $sameContinentCallsTotalCount = 0;

    // Total duration of customer's calls within same continent: (int) 191 of seconds
    public int $sameContinentCallsTotalDuration = 0;

    // Number of all customer's calls: (int) 8
    public int $totalNumberOfAllCustomersCalls = 0;

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
     * @return int
     */
    public function setSameContinentCallsTotalCount(int $sameContinentCallsTotalCount): int
    {
        $this->sameContinentCallsTotalCount = $this->sameContinentCallsTotalCount + $sameContinentCallsTotalCount;

        return $this->sameContinentCallsTotalCount;
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
     * @return int
     */
    public function setSameContinentCallsTotalDuration(int $sameContinentCallsTotalDuration): int
    {
        $this->sameContinentCallsTotalDuration = $this->sameContinentCallsTotalDuration + $sameContinentCallsTotalDuration;

        return$this->sameContinentCallsTotalDuration;
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
