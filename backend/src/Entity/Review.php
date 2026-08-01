<?php

namespace App\Entity;

use App\Repository\ReviewRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ReviewRepository::class)]
#[ORM\Table(name: 'reviews')]
#[ORM\UniqueConstraint(name: 'idx_reviews_unique', columns: ['booking_id', 'user_id'])]
#[ORM\HasLifecycleCallbacks]
class Review
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['review:read', 'car:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Booking::class, inversedBy: 'reviews')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Booking $booking = null;

    #[ORM\ManyToOne(inversedBy: 'reviews')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Groups(['review:read', 'review:write', 'car:read'])]
    private ?Car $car = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'reviews')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Range(min: 1, max: 5, notInRangeMessage: 'Рейтинг должен быть от 1 до 5')]
    #[Groups(['review:read', 'review:write', 'car:read'])]
    private ?int $rating = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    #[Groups(['review:read', 'review:write'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['review:read', 'review:write'])]
    private ?string $comment = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['review:read', 'review:write'])]
    private ?string $pros = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['review:read', 'review:write'])]
    private ?string $cons = null;

    #[ORM\Column(options: ['default' => false])]
    #[Groups(['review:read', 'review:write'])]
    private ?bool $isVerified = false;

    #[ORM\Column(options: ['default' => 0])]
    #[Assert\PositiveOrZero]
    #[Groups(['review:read'])]
    private ?int $helpfulCount = 0;

    #[ORM\Column]
    #[Groups(['review:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['review:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->isVerified = false;
        $this->helpfulCount = 0;
    }

    #[ORM\PreUpdate]
    public function updateTimestamps(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBooking(): ?Booking
    {
        return $this->booking;
    }

    public function setBooking(?Booking $booking): static
    {
        $this->booking = $booking;

        return $this;
    }

    public function getCar(): ?Car
    {
        return $this->car;
    }

    public function setCar(?Car $car): static
    {
        $this->car = $car;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getPros(): ?string
    {
        return $this->pros;
    }

    public function setPros(?string $pros): static
    {
        $this->pros = $pros;

        return $this;
    }

    public function getCons(): ?string
    {
        return $this->cons;
    }

    public function setCons(?string $cons): static
    {
        $this->cons = $cons;

        return $this;
    }

    public function isVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getHelpfulCount(): ?int
    {
        return $this->helpfulCount;
    }

    public function setHelpfulCount(int $helpfulCount): static
    {
        $this->helpfulCount = $helpfulCount;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getRatingLabel(): string
    {
        return match($this->rating) {
            1 => 'Ужасно',
            2 => 'Плохо',
            3 => 'Средне',
            4 => 'Хорошо',
            5 => 'Отлично',
            default => 'Не оценено'
        };
    }

    public function getRatingStars(): string
    {
        return str_repeat('★', $this->rating ?? 0) . str_repeat('☆', 5 - ($this->rating ?? 0));
    }

    public function getRatingHtml(): string
    {
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            $stars .= $i <= ($this->rating ?? 0) ? '★' : '☆';
        }
        return $stars;
    }

    public function getSummary(): string
    {
        $parts = [];

        if ($this->title) {
            $parts[] = $this->title;
        }

        if ($this->comment) {
            $parts[] = substr($this->comment, 0, 100) . (strlen($this->comment) > 100 ? '...' : '');
        }

        return implode(' - ', $parts);
    }

    public function incrementHelpful(): static
    {
        $this->helpfulCount = ($this->helpfulCount ?? 0) + 1;
        return $this;
    }

    public function decrementHelpful(): static
    {
        if (($this->helpfulCount ?? 0) > 0) {
            $this->helpfulCount = $this->helpfulCount - 1;
        }
        return $this;
    }
}
