<?php

namespace App;

final class Call
{
    public int $customerId;

    public string $createdAt;

    public int $duration;

    public int $phoneNumber;

    public string $ip;

    /**
     * Call constructor.
     * @param int $customerId
     * @param string $createdAt
     * @param int $duration
     * @param int $phoneNumber
     * @param string $ip
     */
    public function __construct(int $customerId, string $createdAt, int $duration, int $phoneNumber, string $ip)
    {
        $this->customerId = $customerId;
        $this->createdAt = $createdAt;
        $this->duration = $duration;
        $this->phoneNumber = $phoneNumber;
        $this->ip = $ip;
    }

    /**
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    /**
     * @return int
     */
    public function getDuration(): int
    {
        return intval($this->duration);
    }

    /**
     * @return int
     */
    public function getPhoneNumber(): int
    {
        return $this->phoneNumber;
    }

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @return int
     */
    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    /**
     * @param int $customerId
     */
    public function setCustomerId(int $customerId): void
    {
        $this->customerId = $customerId;
    }

    public function __toString(): string
    {
        return (string) $this->customerId;
    }
}
