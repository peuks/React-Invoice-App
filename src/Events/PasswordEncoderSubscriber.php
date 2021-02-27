<?php

namespace App\Events;

use App\Entity\User;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use ApiPlatform\Core\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Permet le chiffrage du mot de passe avant son enrengistrement en BDD
 */
class PasswordEncoderSubscriber implements EventSubscriberInterface
{
    /**
     * @var UserPasswordEncoderInterface
     */
    protected $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }


    /**
     * Retourne une liste des méthodes que l'on peut brancher à des événements .
     */
    public static function getSubscribedEvents()
    {
        /**
         * Liste des méthodes que l'on veut brancher à des évènements.
         * On appelle la fonction encodePassword avant l'évènement 
         * d'enrengistrement de la donnée
         * 
         */
        return [KernelEvents::VIEW => ['encodePassword', EventPriorities::PRE_WRITE]];
    }

    public function encodePassword(ViewEvent $event)
    {
        /**
         * Récupérer le résultat du controller api platform
         * Dans notre cas il s'agit d'un User
         * @var ViewEvent
         */
        $user = $event->getControllerResult();

        // Renvoie la méthode utilisée GET POST, PUT ...
        $method = $event->getRequest()->getMethod();

        /**
         * Encoder le password d'un utilisateur si les conditions sont remplies.
         * Récupérer un User via la method POST ( on parle d'une création
         *  d'un utilisateur)
         */
        if ($user instanceof User && $method === "POST") {
            /**
             * Récupérer le mot de passe en claire et le définir encodée
             * @var string
             */
            $encodedPassword = $this->encodePassword($user, $user->getPassword());
            $user->setPassword($encodedPassword);
        }
    }
}
