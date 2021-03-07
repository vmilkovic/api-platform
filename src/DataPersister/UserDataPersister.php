<?php

namespace App\DataPersister;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Security;
use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserDataPersister implements ContextAwareDataPersisterInterface {
    
    /**
     * @var DataPersisterInterface
     */
    private $decoratedDataPersister;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @Var Security
     */
    private $security;

    public function __construct(DataPersisterInterface $decoratedDataPersister, UserPasswordEncoderInterface $userPasswordEncoder, LoggerInterface $logger, Security $security)
    {
        $this->decoratedDataPersister = $decoratedDataPersister;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->logger = $logger;
        $this->security = $security;
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof User;
    }

    /**
     * @param User $data
     */
    public function persist($data, array $context = [])
    {
        
        if(($context['item_operation_name'] ?? null) === 'put'){
            $this->logger->info(sprintf('User %s is being updated', $data->getId()));
        }

        if(!$data->getId()){
            // take action for registered user
            $this->logger->info(sprintf('User %s just registered! Eureka!', $data->getEmail()));
        }

        if($data->getPlainPassword()){
            $data->setPassword(
                $this->userPasswordEncoder->encodePassword($data, $data->getPlainPassword())
            );

            $data->eraseCredentials();
        }

        // now handled in listener
        // $data->setIsMe($this->security->getUser() === $data);

        $this->decoratedDataPersister->persist($data);
    }

    public function remove($data, array $context = [])
    {
        $this->decoratedDataPersister->remove($data);
    }
}