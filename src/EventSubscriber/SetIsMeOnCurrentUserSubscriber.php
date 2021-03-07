<?php

namespace App\EventSubscriber;

use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SetIsMeOnCurrentUserSubscriber implements EventSubscriberInterface
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function onRequestEvent(RequestEvent $event)
    {   
        if(!$event->isMasterRequest()){
            return;
        }
        
        /** @var User|null $user */
        $user = $this->security->getUser();

        if(!$user){
            return;
        }

        $user->setIsMe(true);
    }

    public static function getSubscribedEvents()
    {
        return [
            RequestEvent::class => 'onRequestEvent',
        ];
    }
}
