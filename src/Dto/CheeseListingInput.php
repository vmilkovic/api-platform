<?php

namespace App\Dto;

use App\Entity\User;
use App\Entity\CheeseListing;
use App\Validator\IsValidOwner;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\SerializedName;

class CheeseListingInput
{
    /**
     * @var string
     * @Groups({"cheese:write", "user:write"})
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min=2,
     *      max=50,
     *      maxMessage="Describe your cheese in 50 chars or less"
     * )
     */
    public $title;

    /**
     * @var int
     * @Groups({"cheese:write","user:write"})
     * @Assert\NotBlank()
     */
    public $price; 
    
    /**
     * @var User
     * @Groups({"cheese:collection:post"})
     * @IsValidOwner()
     */
    public $owner;

    /**
     * @var bool
     * @Groups({"cheese:write"})
     */
    public $isPublished = false;
    
    /**
     * @Assert\NotBlank()
     */
    public $description;

    /**
     * The description of the cheese as raw text.
     * 
     * @Groups({"cheese:write", "user:write"})
     * @SerializedName("description")
     */
    public function setTextDescription(string $description): self
    {
        $this->description = nl2br($description);

        return $this;
    }

    public static function createFromEntity(?CheeseListing $cheeseListing): self
    {
        $dto = new CheeseListingInput();

        if (!$cheeseListing) {
            return $dto;
        }

        $dto->title = $cheeseListing->getTitle();
        $dto->price = $cheeseListing->getPrice();
        $dto->description = $cheeseListing->getDescription();
        $dto->owner = $cheeseListing->getOwner();
        $dto->isPublished = $cheeseListing->getIsPublished();

        return $dto;
    }

    public function createOrUpdateEntity(?CheeseListing $cheeseListing): CheeseListing
    {
        if (!$cheeseListing) {
            $cheeseListing = new CheeseListing($this->title);
        }

        $cheeseListing->setDescription($this->description);
        $cheeseListing->setPrice($this->price);
        $cheeseListing->setOwner($this->owner);
        $cheeseListing->setIsPublished($this->isPublished);

        return $cheeseListing;
    }
}