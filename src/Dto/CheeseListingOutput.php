<?php

namespace App\Dto;

use Carbon\Carbon;
use App\Entity\CheeseListing;
use Symfony\Component\Serializer\Annotation\Groups;

class CheeseListingOutput
{

    /**
     * The title of this listing
     * 
     * @var string
     * @Groups({"cheese:read", "user:read"})
     */
    public $title;

    /**
     * @var string
     * @Groups({"cheese:read"})
     */
    public $description;

     /**
     * @var int
     * @Groups({"cheese:read", "user:read"})
     */
    public $price;

    public $createdAt;

    /**
     * @var \App\Entity\User
     * @Groups({"cheese:read"})
     */
    public $owner;
    
    /**
     * @Groups("cheese:read")
     */
    public function getShortDescription(): ?string
    {
        if (strlen($this->description) < 40) {
            return $this->description;
        }

        return substr($this->description, 0, 40) . '...';
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * How long ago in text that this cheese listing was added.
     * 
     * @Groups({"cheese:read"})
     */
    public function getCreatedAtAgo(): string
    {
        return Carbon::instance($this->createdAt)->diffForHumans();
    }

    public static function createFromEntity(CheeseListing $cheeseListing): self
    {
        $output = new CheeseListingOutput();
        $output->title = $cheeseListing->getTitle();
        $output->description = $cheeseListing->getDescription();
        $output->price = $cheeseListing->getPrice();
        $output->owner = $cheeseListing->getOwner();
        $output->createdAt = $cheeseListing->getCreatedAt();

        return $output;
    }
}