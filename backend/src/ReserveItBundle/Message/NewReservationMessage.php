<?php

namespace App\ReserveItBundle\Message;

class NewReservationMessage
{
    public function __construct(
        private int $reservationId,
        private string $roomName,
        private string $reservedBy,
        private string $date,
        private string $startTime,
        private string $endTime
    ) {
    }

    public function getReservationId(): int
    {
        return $this->reservationId;
    }

    public function getRoomName(): string
    {
        return $this->roomName;
    }

    public function getReservedBy(): string
    {
        return $this->reservedBy;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getStartTime(): string
    {
        return $this->startTime;
    }

    public function getEndTime(): string
    {
        return $this->endTime;
    }
}
