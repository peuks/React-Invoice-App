<?php

namespace App\Events;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JwtCreatedSubscriber
{
    /**
     * La fonction est appellé automatiquement lors d'un évènement de création de token 
     * pour nous permettre sa modification ou non
     */
    public function updateJwtData(JWTCreatedEvent $event)
    {
        /**
         * $event->getData()
         * 
         * Il est possible de travailler sur les datas d'un événement
         * Dans ce cas on récupère le PAYLOAD, c'est à dire les données
         * qui sont dans le token. Ces données sont modifiables.
         * 
         * On a donc un utilisateur mais on veut renseigner plus de valeur.
         * Par défaut on a un tableau avec le ROLE et userName en clée / valeur.
         * On voudrait rajouter dans le tableau firstName et lastName
         * 
         * Note : La clef est définit par le dev tandis que la valeur est obtenue 
         *        via les méthodes de la Class User ( dans notre cas ) 
         */

        /**
         * @var User
         */
        $user = $event->getUser();

        $data = $event->getData();

        $data['firstName'] = $user->getFirstName();
        $data['lastName'] = $user->getLastName();

        $event->setData($data);
    }
}
