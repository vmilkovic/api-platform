<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\CheeseListing;
use App\Entity\CheeseNotification;
use Doctrine\ORM\EntityManagerInterface;

class CheeseListingDataPersister implements DataPersisterInterface {

    private $decoratedDataPersister;

    private $entityManager;

    public function __construct(DataPersisterInterface $decoratedDataPersister, EntityManagerInterface $entityManager)
    {
        $this->decoratedDataPersister = $decoratedDataPersister;
        $this->entityManager = $entityManager;
    }

    public function supports($data): bool
    {
        return $data instanceof CheeseListing;
    }

    /**
     * @param CheeseListing $data
     */
    public function persist($data)
    {
        $originalData = $this->entityManager->getUnitOfWork()->getOriginalEntityData($data);
        $wasAlredyPublished = $originalData['isPublished'] ?? false;

        if($data->getIsPublished() && !$wasAlredyPublished){
            $notification = new CheeseNotification($data, 'Cheese listing was created!');
            $this->entityManager->persist($notification);
            $this->entityManager->flush();
        }

        $this->decoratedDataPersister->persist($data);
    }

    public function remove($data)
    {
        $this->decoratedDataPersister->persist($data);
    }

}