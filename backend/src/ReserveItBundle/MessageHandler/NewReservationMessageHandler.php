<?php

namespace App\ReserveItBundle\MessageHandler;

use App\ReserveItBundle\Message\NewReservationMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Psr\Log\LoggerInterface;

#[AsMessageHandler]
class NewReservationMessageHandler
{
    public function __construct(
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(NewReservationMessage $message): void
    {
        // Here you would typically send notifications, emails, etc.
        $this->logger->info('New reservation created', [
            'reservation_id' => $message->getReservationId(),
            'room' => $message->getRoomName(),
            'reserved_by' => $message->getReservedBy(),
            'date' => $message->getDate(),
            'start_time' => $message->getStartTime(),
            'end_time' => $message->getEndTime(),
        ]);
    }
}
