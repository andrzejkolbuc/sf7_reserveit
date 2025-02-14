<?php

namespace App\ReserveItBundle\EventSubscriber;

use App\ReserveItBundle\Event\ReservationCreatedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ReservationEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private LoggerInterface $logger
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ReservationCreatedEvent::NAME => 'onReservationCreated',
        ];
    }

    public function onReservationCreated(ReservationCreatedEvent $event): void
    {
        $reservation = $event->getReservation();
        $room = $reservation->getRoom();
        
        // Log the reservation
        $this->logger->info('New reservation created', [
            'reservation_id' => $reservation->getId(),
            'room_id' => $room->getId(),
            'room_name' => $room->getName(),
            'start_time' => $reservation->getStartTime()->format('Y-m-d H:i:s'),
            'end_time' => $reservation->getEndTime()->format('Y-m-d H:i:s'),
            'title' => $reservation->getTitle()
        ]);

        // Here you can add more actions like:
        // - Sending email notifications
        // - Creating calendar events
        // - Updating statistics
        // - Triggering external integrations
    }
}
