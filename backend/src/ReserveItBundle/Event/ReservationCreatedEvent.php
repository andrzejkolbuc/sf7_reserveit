<?php

namespace App\ReserveItBundle\Event;

use App\ReserveItBundle\Entity\Reservation;
use Symfony\Contracts\EventDispatcher\Event;

class ReservationCreatedEvent extends Event
{
    public const NAME = 'reservation.created';

    public function __construct(
        private Reservation $reservation
    ) {
    }

    public function getReservation(): Reservation
    {
        return $this->reservation;
    }
}
