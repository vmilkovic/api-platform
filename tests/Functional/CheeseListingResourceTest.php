<?php

namespace App\Tests\Functional;

use App\Entity\CheeseListing;
use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class CheeseListingResourceTest extends CustomApiTestCase {

    use ReloadDatabaseTrait;

    public function testCreateCheeseListing(){
        
        $client = self::createClient();

        $client->request('POST', '/api/cheeses',  [
            'json' => [],
        ]);
        $this->assertResponseStatusCodeSame(401);
        
        $autheticatedUser = $this->createUserAndLogIn($client, 'cheeseplease@example.com', 'foo');
        $otherUser = $this->createUser('otheruser@example.com', 'foo');

        $data = [
            'title' => 'Mystery cheese... kinda green',
            'description' => 'What mystery does it hold?',
            'price' => 5000 
        ];

        $client->request('POST', '/api/cheeses',  [
            'json' => $data,
        ]);
        $this->assertResponseStatusCodeSame(201);

        $client->request('POST', '/api/cheeses',  [
            'json' => $data + [
                'owner' => '/api/users/'.$otherUser->getId()
            ],
        ]);
        $this->assertResponseStatusCodeSame(400, 'not passing the correct owner');

        
        $client->request('POST', '/api/cheeses',  [
            'json' => $data + [
                'owner' => '/api/users/'.$autheticatedUser->getId()
            ],
        ]);
        $this->assertResponseStatusCodeSame(201);
    }

    public function testUpdateCheeseListing(){

        $client = self::createClient();
        $user1 = $this->createUser('user1@example.com', 'foo');
        $user2 = $this->createUser('user2@example.com', 'foo');

        $cheeseListing = new CheeseListing('Block of cheddar');
        $cheeseListing->setOwner($user1);
        $cheeseListing->setPrice(1000);
        $cheeseListing->setDescription('mmmm');
        $cheeseListing->setIsPublished(true);

        $em = $this->getEntityManager();
        $em->persist($cheeseListing);
        $em->flush();

        $this->logIn($client, 'user2@example.com', 'foo');
        $client->request('PUT', '/api/cheeses/'.$cheeseListing->getId(), [
            'json' => [
                'title' => 'updated',
                'owner' => '/api/users/'.$user2->getId()
            ]
        ]);

        $this->assertResponseStatusCodeSame(403);

        $this->logIn($client, 'user1@example.com', 'foo');
        $client->request('PUT', '/api/cheeses/'.$cheeseListing->getId(), [
            'json' => [
                'title' => 'updated'
            ]
        ]);

        $this->assertResponseStatusCodeSame(200);
    }

    public function testGetCheeseListingCollection(){
        $client = self::createClient();
        $user = $this->createUser('cheeseplease@example.com', 'bar');
        
        $cheeseListing1 = new CheeseListing('cheese1');
        $cheeseListing1->setOwner($user);
        $cheeseListing1->setPrice(1000);
        $cheeseListing1->setDescription('cheese1');

        $cheeseListing2 = new CheeseListing('cheese2');
        $cheeseListing2->setOwner($user);
        $cheeseListing2->setPrice(2000);
        $cheeseListing2->setDescription('cheese2');
        $cheeseListing2->setIsPublished(true);

        $cheeseListing3 = new CheeseListing('cheese3');
        $cheeseListing3->setOwner($user);
        $cheeseListing3->setPrice(3000);
        $cheeseListing3->setDescription('cheese3');
        $cheeseListing3->setIsPublished(true);

        $em = $this->getEntityManager();
        $em->persist($cheeseListing1);
        $em->persist($cheeseListing2);
        $em->persist($cheeseListing3);
        $em->flush();

        $client->request('GET', '/api/cheeses');
        $this->assertJsonContains(['hydra:totalItems' => 2]);
    }

    public function testGetCheeseListingItem(){
        $client = self::createClient();
        $user = $this->createUserAndLogIn($client, 'cheeseplease@example.com', 'bar');
        
        $cheeseListing = new CheeseListing('cheese');
        $cheeseListing->setOwner($user);
        $cheeseListing->setPrice(1000);
        $cheeseListing->setDescription('cheese');
        $cheeseListing->setIsPublished(false);

        $em = $this->getEntityManager();
        $em->persist($cheeseListing);
        $em->flush();

        $client->request('GET', '/api/cheeses/'.$cheeseListing->getId());
        $this->assertResponseStatusCodeSame(404);

        $client->request('GET', '/api/users/'.$user->getId());
        $data = $client->getResponse()->toArray();
        $this->assertEmpty($data['cheeseListings']);
    }
}