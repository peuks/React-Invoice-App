<?php

namespace App\Events;

use App\Entity\User;
use App\Entity\Customer;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use ApiPlatform\Core\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CustomerUserSubscriber implements EventSubscriberInterface
{
    protected $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * Retourne une liste des méthodes que l'on peut brancher à des événements .
     */
    public static function getSubscribedEvents()
    {
        /**
         * Liste des méthodes que l'on veut brancher à des évènements.
         * On appelle la fonction setUserCustomer avant l'évènement 
         * de validation des données
         * 
         */
        return [KernelEvents::VIEW => ['setUserForCustomer', EventPriorities::PRE_VALIDATE]];
    }

    public function setUserForCustomer(ViewEvent $event)
    {
        /**
         * Récupérer le résultat du controller
         * Dans notre cas il s'agit dun 
   
         */
        $customer =  $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if ($customer instanceof Customer && $method === "POST") {

            /**
             * Récupére l'utilisateur actuel 
             */
            $user = $this->security->getUser();

            /**
             * Rattacher notre Customer à l'utilisateur actuellement connecté
             * Condition obligatoire pour passer la validation Symfony
             */
            $customer->setUser($user);
        }
    }
}
