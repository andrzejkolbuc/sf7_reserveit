<?php

namespace App\Tests\ReserveItBundle\Entity;

use App\ReserveItBundle\Entity\Room;
use App\ReserveItBundle\Entity\Reservation;
use PHPUnit\Framework\TestCase;

class RoomTest extends TestCase
{
    private Room $room;

    protected function setUp(): void
    {
        $this->room = new Room();
        $this->room->setName('Test Room');
        $this->room->setCapacity(10);
        $this->room->setDescription('Test Description');
    }

    public function testGettersAndSetters(): void
    {
        $this->assertEquals('Test Room', $this->room->getName());
        $this->assertEquals(10, $this->room->getCapacity());
        $this->assertEquals('Test Description', $this->room->getDescription());
    }

    public function testActiveReservations(): void
    {
        $now = new \DateTime('2025-02-14T02:44:14+01:00');
        
        // Past reservation
        $pastReservation = new Reservation();
        $pastReservation->setStartTime(new \DateTime('2025-02-13T00:00:00+01:00'));
        $pastReservation->setEndTime(new \DateTime('2025-02-13T01:00:00+01:00'));
        $pastReservation->setTitle('Past Meeting');
        $pastReservation->setRoom($this->room);
        
        // Current reservation
        $currentReservation = new Reservation();
        $currentReservation->setStartTime(new \DateTime('2025-02-14T14:00:00+01:00'));
        $currentReservation->setEndTime(new \DateTime('2025-02-14T15:00:00+01:00'));
        $currentReservation->setTitle('Current Meeting');
        $currentReservation->setRoom($this->room);
        
        // Future reservation
        $futureReservation = new Reservation();
        $futureReservation->setStartTime(new \DateTime('2025-02-14T16:00:00+01:00'));
        $futureReservation->setEndTime(new \DateTime('2025-02-14T17:00:00+01:00'));
        $futureReservation->setTitle('Future Meeting');
        $futureReservation->setRoom($this->room);

        // Add reservations to room's collection
        $reflection = new \ReflectionClass($this->room);
        $reservationsProperty = $reflection->getProperty('reservations');
        $reservationsProperty->setAccessible(true);
        $reservationsProperty->setValue($this->room, new \Doctrine\Common\Collections\ArrayCollection([
            $pastReservation,
            $currentReservation,
            $futureReservation
        ]));

        $activeReservations = $this->room->getActiveReservations();
        
        // Should only include current and future reservations
        $this->assertCount(2, $activeReservations);
        
        // Verify the structure of active reservations
        foreach ($activeReservations as $reservation) {
            $this->assertArrayHasKey('id', $reservation);
            $this->assertArrayHasKey('startTime', $reservation);
            $this->assertArrayHasKey('endTime', $reservation);
            $this->assertArrayHasKey('title', $reservation);
        }
    }
}
