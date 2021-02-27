<?php

namespace App\Helper;

use App\Entity\Invoice;

class CheckAndSetDate
{
    /**
     * Renvoie la date actuel si aucune date n'est précisée
     * @param Invoice $invoice
     * @return Date|'' 
     *  
     */
    public function checkAndSet(Invoice $invoice)
    {
        return ($invoice->getSentAt()) ?: $invoice->setSentAt(new \DateTime());
    }
}
