<?php

namespace App\Events;

use App\Entity\Invoice;
use App\Repository\InvoiceRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use ApiPlatform\Core\EventListener\EventPriorities;
use App\Helper\CheckAndSetDate;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class InvoiceChronoIncrement implements EventSubscriberInterface
{
    protected $security;
    protected $repository;
    protected $date;

    public function __construct(Security $security, InvoiceRepository $repository, CheckAndSetDate $date)
    {
        $this->repository = $repository;
        $this->security = $security;
        $this->date = $date;
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
         * Pour passer la validation, l'incrémentation doit être définie.
         */
        return [KernelEvents::VIEW => ['setChronoForInvoice', EventPriorities::PRE_VALIDATE]];
    }

    public function setChronoForInvoice(ViewEvent $event)
    {
        /**
         * Récupérer le résultat du controller
         * Dans notre cas il s'agit d'un Invoice
         * Dans mon invoice je n'ai pas le chrono mais j'ai le customer en question !
         * @var Invoice 
         */

        $invoice =  $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        /**
         * * Si l'on récupère une instance Invoice en Post alors
         * * Récupérer l'utilisateur
         * * Récupérer le chrono de la dernière facture du customer en cours 
         *  ( il est renseigné dans l'instance invoice)
         * * Définir le chrono
         */
        if ($invoice instanceof Invoice && $method === "POST") {

            $user = $this->security->getUser();

            $nextChrono = $this->repository->findNextChrono($user);
            $invoice->setChrono($nextChrono);
            $this->date->checkAndSet($invoice);
        }
    }
}
