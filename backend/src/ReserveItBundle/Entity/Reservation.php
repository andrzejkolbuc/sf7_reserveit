<?php

namespace App\ReserveItBundle\Entity;

use App\ReserveItBundle\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['reservation:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    #[Groups(['reservation:read', 'reservation:write'])]
    private ?Room $room = null;

    #[ORM\Column(type: "datetime")]
    #[Assert\NotNull]
    #[Groups(['reservation:read', 'reservation:write'])]
    private ?\DateTimeInterface $startTime = null;

    #[ORM\Column(type: "datetime")]
    #[Assert\NotNull]
    #[Assert\Expression(
        "this.getEndTime() > this.getStartTime()",
        message: "End time must be after start time"
    )]
    #[Groups(['reservation:read', 'reservation:write'])]
    private ?\DateTimeInterface $endTime = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 255)]
    #[Groups(['reservation:read', 'reservation:write'])]
    private ?string $title = null;

    #[ORM\Column(length: 1000, nullable: true)]
    #[Groups(['reservation:read', 'reservation:write'])]
    private ?string $description = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function setRoom(?Room $room): static
    {
        $this->room = $room;
        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeInterface $startTime): static
    {
        $this->startTime = $startTime;
        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTimeInterface $endTime): static
    {
        $this->endTime = $endTime;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }
}
