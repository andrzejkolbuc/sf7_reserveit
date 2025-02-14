<?php

namespace App\ReserveItBundle\Controller;

use App\ReserveItBundle\Entity\Reservation;
use App\ReserveItBundle\Repository\ReservationRepository;
use App\ReserveItBundle\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/reservations')]
class ReservationController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ReservationRepository $reservationRepository,
        private RoomRepository $roomRepository,
        private ValidatorInterface $validator
    ) {
    }

    #[Route('', name: 'reservation_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $reservations = $this->reservationRepository->findAll();
        return $this->json(['data' => $reservations], context: ['groups' => ['reservation:read']]);
    }

    #[Route('', name: 'reservation_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $room = $this->roomRepository->find($data['roomId']);
        if (!$room) {
            return $this->json(['error' => 'Room not found'], 404);
        }

        $reservation = new Reservation();
        $reservation->setRoom($room);
        $reservation->setStartTime(new \DateTime($data['startTime']));
        $reservation->setEndTime(new \DateTime($data['endTime']));
        $reservation->setTitle($data['title']);
        $reservation->setDescription($data['description'] ?? null);

        $errors = $this->validator->validate($reservation);
        if (count($errors) > 0) {
            return $this->json(['error' => (string) $errors], 400);
        }

        // Check for overlapping reservations
        $overlapping = $this->reservationRepository->findOverlapping(
            $room,
            $reservation->getStartTime(),
            $reservation->getEndTime()
        );

        if ($overlapping) {
            return $this->json(['error' => 'Room is already reserved for this time period'], 409);
        }

        $this->entityManager->persist($reservation);
        $this->entityManager->flush();

        return $this->json(['data' => $reservation], 201, context: ['groups' => ['reservation:read']]);
    }

    #[Route('/{id}', name: 'reservation_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $reservation = $this->reservationRepository->find($id);
        if (!$reservation) {
            return $this->json(['error' => 'Reservation not found'], 404);
        }

        return $this->json(['data' => $reservation], context: ['groups' => ['reservation:read']]);
    }

    #[Route('/{id}', name: 'reservation_update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $reservation = $this->reservationRepository->find($id);
        if (!$reservation) {
            return $this->json(['error' => 'Reservation not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['roomId'])) {
            $room = $this->roomRepository->find($data['roomId']);
            if (!$room) {
                return $this->json(['error' => 'Room not found'], 404);
            }
            $reservation->setRoom($room);
        }

        if (isset($data['startTime'])) {
            $reservation->setStartTime(new \DateTime($data['startTime']));
        }
        if (isset($data['endTime'])) {
            $reservation->setEndTime(new \DateTime($data['endTime']));
        }
        if (isset($data['title'])) {
            $reservation->setTitle($data['title']);
        }
        if (isset($data['description'])) {
            $reservation->setDescription($data['description']);
        }

        $errors = $this->validator->validate($reservation);
        if (count($errors) > 0) {
            return $this->json(['error' => (string) $errors], 400);
        }

        // Check for overlapping reservations
        $overlapping = $this->reservationRepository->findOverlapping(
            $reservation->getRoom(),
            $reservation->getStartTime(),
            $reservation->getEndTime(),
            $reservation->getId()
        );

        if ($overlapping) {
            return $this->json(['error' => 'Room is already reserved for this time period'], 409);
        }

        $this->entityManager->flush();

        return $this->json(['data' => $reservation], context: ['groups' => ['reservation:read']]);
    }

    #[Route('/{id}', name: 'reservation_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $reservation = $this->reservationRepository->find($id);
        if (!$reservation) {
            return $this->json(['error' => 'Reservation not found'], 404);
        }

        $this->entityManager->remove($reservation);
        $this->entityManager->flush();

        return $this->json(null, 204);
    }
}
