<?php

namespace App\ReserveItBundle\Controller;

use App\ReserveItBundle\Entity\Room;
use App\ReserveItBundle\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/rooms')]
class RoomController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RoomRepository $roomRepository,
        private ValidatorInterface $validator
    ) {
    }

    #[Route('', name: 'room_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $rooms = $this->roomRepository->findAll();
        return $this->json(['data' => $rooms], context: ['groups' => ['room:read']]);
    }

    #[Route('', name: 'room_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $room = new Room();
        $room->setName($data['name']);
        $room->setCapacity($data['capacity']);
        $room->setDescription($data['description'] ?? null);

        $errors = $this->validator->validate($room);
        if (count($errors) > 0) {
            return $this->json(['error' => (string) $errors], 400);
        }

        $this->entityManager->persist($room);
        $this->entityManager->flush();

        return $this->json(['data' => $room], 201, context: ['groups' => ['room:read']]);
    }

    #[Route('/{id}', name: 'room_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $room = $this->roomRepository->find($id);
        if (!$room) {
            return $this->json(['error' => 'Room not found'], 404);
        }

        return $this->json(['data' => $room], context: ['groups' => ['room:read']]);
    }

    #[Route('/{id}', name: 'room_update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $room = $this->roomRepository->find($id);
        if (!$room) {
            return $this->json(['error' => 'Room not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['name'])) {
            $room->setName($data['name']);
        }
        if (isset($data['capacity'])) {
            $room->setCapacity($data['capacity']);
        }
        if (isset($data['description'])) {
            $room->setDescription($data['description']);
        }

        $errors = $this->validator->validate($room);
        if (count($errors) > 0) {
            return $this->json(['error' => (string) $errors], 400);
        }

        $this->entityManager->flush();

        return $this->json(['data' => $room], context: ['groups' => ['room:read']]);
    }

    #[Route('/{id}', name: 'room_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $room = $this->roomRepository->find($id);
        if (!$room) {
            return $this->json(['error' => 'Room not found'], 404);
        }

        try {
            $this->entityManager->remove($room);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            return $this->json(['error' => 'Cannot delete room with existing reservations'], 409);
        }

        return $this->json(null, 204);
    }
}
