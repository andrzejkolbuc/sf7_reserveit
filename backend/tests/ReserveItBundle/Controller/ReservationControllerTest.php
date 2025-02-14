<?php

namespace App\Tests\ReserveItBundle\Controller;

use App\ReserveItBundle\Entity\Room;
use App\ReserveItBundle\Entity\Reservation;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;

class ReservationControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;
    private $room;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        
        // Clear the test database
        $this->entityManager->createQuery('DELETE FROM App\ReserveItBundle\Entity\Reservation')->execute();
        $this->entityManager->createQuery('DELETE FROM App\ReserveItBundle\Entity\Room')->execute();
        
        // Create a test room
        $this->room = new Room();
        $this->room->setName('Test Room');
        $this->room->setCapacity(10);
        $this->room->setDescription('Test Description');
        
        $this->entityManager->persist($this->room);
        $this->entityManager->flush();
    }

    public function testCreateReservation(): void
    {
        $reservationData = [
            'roomId' => $this->room->getId(),
            'title' => 'Test Meeting',
            'startTime' => '2025-02-14T14:00:00+01:00',
            'endTime' => '2025-02-14T15:00:00+01:00',
            'description' => 'Test Description'
        ];

        $this->client->request(
            'POST',
            '/api/reservations',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($reservationData)
        );

        $response = $this->client->getResponse();
        
        $this->assertEquals(201, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        
        $this->assertArrayHasKey('data', $content);
        $this->assertEquals('Test Meeting', $content['data']['title']);
        $this->assertEquals($this->room->getId(), $content['data']['room']['id']);
    }

    public function testCreateOverlappingReservation(): void
    {
        // Create first reservation
        $reservation = new Reservation();
        $reservation->setRoom($this->room);
        $reservation->setTitle('First Meeting');
        $reservation->setStartTime(new \DateTime('2025-02-14T14:00:00+01:00'));
        $reservation->setEndTime(new \DateTime('2025-02-14T16:00:00+01:00'));
        
        $this->entityManager->persist($reservation);
        $this->entityManager->flush();

        // Try to create overlapping reservation
        $overlappingData = [
            'roomId' => $this->room->getId(),
            'title' => 'Overlapping Meeting',
            'startTime' => '2025-02-14T14:30:00+01:00',
            'endTime' => '2025-02-14T15:30:00+01:00',
            'description' => 'Should not be allowed'
        ];

        $this->client->request(
            'POST',
            '/api/reservations',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($overlappingData)
        );

        $response = $this->client->getResponse();
        
        $this->assertEquals(409, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $content);
    }

    public function testListReservations(): void
    {
        // Create test reservation
        $reservation = new Reservation();
        $reservation->setRoom($this->room);
        $reservation->setTitle('Test Meeting');
        $reservation->setStartTime(new \DateTime('2025-02-14T14:00:00+01:00'));
        $reservation->setEndTime(new \DateTime('2025-02-14T15:00:00+01:00'));
        
        $this->entityManager->persist($reservation);
        $this->entityManager->flush();

        // Test list endpoint
        $this->client->request('GET', '/api/reservations');
        $response = $this->client->getResponse();
        
        $this->assertEquals(200, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        
        $this->assertArrayHasKey('data', $content);
        $this->assertCount(1, $content['data']);
        $this->assertEquals('Test Meeting', $content['data'][0]['title']);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        
        // Clean up the test database
        $this->entityManager->createQuery('DELETE FROM App\ReserveItBundle\Entity\Reservation')->execute();
        $this->entityManager->createQuery('DELETE FROM App\ReserveItBundle\Entity\Room')->execute();
        $this->entityManager->flush();
        
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
