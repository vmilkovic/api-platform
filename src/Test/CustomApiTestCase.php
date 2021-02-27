<?php

namespace App\Test;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CustomApiTestCase extends ApiTestCase {

    protected function createUser(string $email, string $password): User {
        $user = new User();
        $user->setEmail($email);
        $user->setUsername(substr($email, 0, strpos($email, '@')));

        $encoded = self::$container->get('security.password_encoder')->encodePassword($user, $password); #or self::$container->get(UserPasswordEncoderInterface::class)
       
        $user->setPassword($encoded);

        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }

    protected function logIn(Client $client, string $email, string $password){
        $client->request('POST', '/login',  [
            'json' => [
                'email' => $email,
                'password' => $password
            ],
        ]);

        $this->assertResponseStatusCodeSame(204);
    }

    protected function createUserAndLogIn(Client $client, string $email, string $password){
        $user = $this->createUser($email, $password);

        $this->logIn($client, $email, $password);

        return $user;
    }

    protected function getEntityManager(): EntityManagerInterface {
        return self::$container->get('doctrine')->getManager(); #or self::$container->get(EntityManagerInterface::class);
    }
}