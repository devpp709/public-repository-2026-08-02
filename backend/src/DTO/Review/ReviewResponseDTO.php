<?php

namespace App\DTO\Review;

use App\Entity\Review;
use App\DTO\User\UserResponseDTO;
use App\DTO\Car\CarResponseDTO;
use App\DTO\Booking\BookingResponseDTO;
use Symfony\Component\Serializer\Annotation\Groups;

class ReviewResponseDTO
{
    #[Groups(['review:read', 'car:read'])]
    public int $id;

    #[Groups(['review:read'])]
    public BookingResponseDTO $booking;

    #[Groups(['review:read'])]
    public CarResponseDTO $car;

    #[Groups(['review:read'])]
    public UserResponseDTO $user;

    #[Groups(['review:read', 'car:read'])]
    public int $rating;

    #[Groups(['review:read', 'car:read'])]
    public string $ratingLabel;

    #[Groups(['review:read', 'car:read'])]
    public string $ratingStars;

    #[Groups(['review:read'])]
    public ?string $title;

    #[Groups(['review:read'])]
    public ?string $comment;

    #[Groups(['review:read'])]
    public ?string $pros;

    #[Groups(['review:read'])]
    public ?string $cons;

    #[Groups(['review:read'])]
    public bool $isVerified;

    #[Groups(['review:read'])]
    public int $helpfulCount;

    #[Groups(['review:read'])]
    public string $summary;

    #[Groups(['review:read'])]
    public string $createdAt;

    #[Groups(['review:read'])]
    public string $updatedAt;

    public static function fromEntity(Review $review): self
    {
        $dto = new self();
        $dto->id = $review->getId();
        $dto->booking = BookingResponseDTO::fromEntity($review->getBooking(), false);
        $dto->car = CarResponseDTO::fromEntity($review->getCar(), false);
        $dto->user = UserResponseDTO::fromEntity($review->getUser());
        $dto->rating = $review->getRating();
        $dto->ratingLabel = $review->getRatingLabel();
        $dto->ratingStars = $review->getRatingStars();
        $dto->title = $review->getTitle();
        $dto->comment = $review->getComment();
        $dto->pros = $review->getPros();
        $dto->cons = $review->getCons();
        $dto->isVerified = $review->isVerified();
        $dto->helpfulCount = $review->getHelpfulCount();
        $dto->summary = $review->getSummary();
        $dto->createdAt = $review->getCreatedAt()->format('Y-m-d H:i:s');
        $dto->updatedAt = $review->getUpdatedAt()->format('Y-m-d H:i:s');

        return $dto;
    }

    public static function fromEntities(array $reviews): array
    {
        return array_map(
            fn(Review $review) => self::fromEntity($review),
            $reviews
        );
    }
}
