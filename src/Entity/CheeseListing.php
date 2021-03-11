<?php

namespace App\Entity;

use Carbon\Carbon;
use App\Entity\User;
use App\Dto\CheeseListingInput;
use App\Validator\IsValidOwner;
use App\Dto\CheeseListingOutput;
use Doctrine\ORM\Mapping as ORM;
use App\Validator\ValidIsPublished;
use App\ApiPlatform\CheeseSearchFilter;
use ApiPlatform\Core\Annotation\ApiFilter;
use App\Repository\CheeseListingRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;

/**
 * @ApiResource(
 *      output=CheeseListingOutput::CLASS,
 *      input=CheeseListingInput::CLASS,
 *      itemOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"cheese:read", "cheese:item:get"}}
 *          }, 
 *          "put"={
 *              "security"="is_granted('EDIT', object)",
 *              "security_message"="Only the creator can edit a cheese listing"
 *          },
 *          "delete" = {"security" = "is_granted('ROLE_ADMIN')"}
 *      },
 *      collectionOperations={
 *          "get", 
 *          "post" = {"security" = "is_granted('ROLE_USER')"}
 *      },
 *      shortName="cheese",
 *      attributes={
 *          "pagination_items_per_page"=10,
 *          "formats"={"jsonld", "json", "html", "jsonhal", "csv"={"text/csv"}}
 *      }
 * )
 * @ApiFilter(BooleanFilter::class, properties={"isPublished"})
 * @ApiFilter(SearchFilter::class, properties={
 *      "title": "partial", 
 *      "description": "partial",
 *      "owner": "exact",
 *      "owner.username": "partial"
 * })
 * @ApiFilter(CheeseSearchFilter::class)
 * @ApiFilter(RangeFilter::class, properties={"price"})
 * @ApiFilter(PropertyFilter::class)
 * @ORM\Entity(repositoryClass=CheeseListingRepository::class)
 * @ORM\EntityListeners({"App\Doctrine\CheeseListingSetOwnerListener"})
 * @ValidIsPublished()
 */
class CheeseListing
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min=2,
     *      max=50,
     *      maxMessage="Describe your cheese in 50 chars or less"
     * )
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * The price of this delicious chese, in cents.
     * 
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     */
    private $price;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPublished = false;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="cheeseListings")
     * @ORM\JoinColumn(nullable=false)
     * @IsValidOwner()
     */
    private $owner;

    public function __construct(string $title = null)
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->title = $title;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
}