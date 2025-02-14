<?php

namespace App\ReserveItBundle\EventSubscriber;

use App\ReserveItBundle\Event\ReservationCreatedEvent;
use App\ReserveItBundle\Service\RabbitMQService;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ReservationEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private LoggerInterface $logger,
        private RabbitMQService $rabbitMQService
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

        // Prepare message for RabbitMQ
        $message = [
            'event' => 'reservation.created',
            'reservation' => [
                'id' => $reservation->getId(),
                'room' => [
                    'id' => $room->getId(),
                    'name' => $room->getName(),
                    'capacity' => $room->getCapacity()
                ],
                'start_time' => $reservation->getStartTime()->format('Y-m-d H:i:s'),
                'end_time' => $reservation->getEndTime()->format('Y-m-d H:i:s'),
                'title' => $reservation->getTitle(),
                'description' => $reservation->getDescription(),
                'created_at' => (new \DateTime())->format('Y-m-d H:i:s')
            ]
        ];

        // Publish message to RabbitMQ
        try {
            $this->rabbitMQService->publishReservationMessage($message);
            $this->logger->info('Reservation message published to RabbitMQ', [
                'reservation_id' => $reservation->getId()
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to publish reservation message to RabbitMQ', [
                'reservation_id' => $reservation->getId(),
                'error' => $e->getMessage()
            ]);
        }
    }
}
