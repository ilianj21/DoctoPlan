<?php
// src/EventSubscriber/LoginSuccessSubscriber.php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginSuccessSubscriber implements EventSubscriberInterface
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // cet événement est déclenché juste après une connexion réussie
            InteractiveLoginEvent::class => 'onLoginSuccess',
        ];
    }

    public function onLoginSuccess(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();
        $name = method_exists($user, 'getName')
            ? $user->getName()
            : $user->getUserIdentifier();

        $this->requestStack
            ->getSession()
            ->getFlashBag()
            ->add('success', sprintf('Bienvenue %s, vous êtes maintenant connecté !', $name));
    }
}
