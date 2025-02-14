<?php

namespace App\Tests\ReserveItBundle\Controller;

use App\ReserveItBundle\Entity\Room;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;

class RoomControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        
        // Clear the test database
        $this->entityManager->createQuery('DELETE FROM App\ReserveItBundle\Entity\Reservation')->execute();
        $this->entityManager->createQuery('DELETE FROM App\ReserveItBundle\Entity\Room')->execute();
        $this->entityManager->flush();
    }

    public function testListRooms(): void
    {
        // Create a test room
        $room = new Room();
        $room->setName('Test Room');
        $room->setCapacity(10);
        $room->setDescription('Test Description');
        
        $this->entityManager->persist($room);
        $this->entityManager->flush();

        // Make request
        $this->client->request('GET', '/api/rooms');
        $response = $this->client->getResponse();
        
        // Assert response
        $this->assertEquals(200, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        
        $this->assertArrayHasKey('data', $content);
        $this->assertCount(1, $content['data']);
        $this->assertEquals('Test Room', $content['data'][0]['name']);
        $this->assertEquals(10, $content['data'][0]['capacity']);
        $this->assertEquals('Test Description', $content['data'][0]['description']);
        $this->assertArrayHasKey('activeReservations', $content['data'][0]);
    }

    public function testCreateRoom(): void
    {
        $roomData = [
            'name' => 'New Test Room',
            'capacity' => 15,
            'description' => 'New Test Description'
        ];

        $this->client->request(
            'POST',
            '/api/rooms',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($roomData)
        );

        $response = $this->client->getResponse();
        
        // Assert response
        $this->assertEquals(201, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        
        $this->assertArrayHasKey('data', $content);
        $this->assertEquals('New Test Room', $content['data']['name']);
        $this->assertEquals(15, $content['data']['capacity']);
        $this->assertEquals('New Test Description', $content['data']['description']);
    }

    public function testShowRoom(): void
    {
        // Create a test room
        $room = new Room();
        $room->setName('Test Room');
        $room->setCapacity(10);
        $room->setDescription('Test Description');
        
        $this->entityManager->persist($room);
        $this->entityManager->flush();

        // Make request
        $this->client->request('GET', '/api/rooms/' . $room->getId());
        $response = $this->client->getResponse();
        
        // Assert response
        $this->assertEquals(200, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        
        $this->assertArrayHasKey('data', $content);
        $this->assertEquals('Test Room', $content['data']['name']);
        $this->assertEquals(10, $content['data']['capacity']);
        $this->assertEquals('Test Description', $content['data']['description']);
        $this->assertArrayHasKey('activeReservations', $content['data']);
    }

    public function testShowNonExistentRoom(): void
    {
        $this->client->request('GET', '/api/rooms/999999');
        $response = $this->client->getResponse();
        
        $this->assertEquals(404, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $content);
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
