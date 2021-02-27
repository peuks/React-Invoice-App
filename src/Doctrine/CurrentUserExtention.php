<?php

namespace App\Doctrine;

use App\Entity\Customer;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use App\Entity\Invoice;

class CurrentUserExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    protected $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }


    /**
     * Doctrine peut effectuer des requêtes pou récupérer des collections
     * Il se rend compte que l'on a une extension et appliquer des coorectifs | ameiliorations
     *
     * @param QueryBuilder $queryBuilder il s'agit de la requête Doctrine
     * @param QueryNameGeneratorInterface $queryNameGenerator
     * @param string $resourceClass Le nom de la class sur laquelle on est entrain de faire des requêtes ( ex : la liste des invoices -> la class Invoice)
     * @param string|null $operationName
     * @return void
     */
    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?string $operationName = null)
    {
        // Obtenir l'utilisateur connecté
        $user =  $this->security->getUser();

        /**
         * Si l'on demande des invoices ou des customers alors agir sur la requee pour qu'elle tienne compte de l'utilisateur
         */

        if ($resourceClass === Customer::class || $resourceClass === Invoice::class) {
            dd($queryBuilder);
        }
    }


    /**
     *
     * @param QueryBuilder $queryBuilder
     * @param QueryNameGeneratorInterface $queryNameGenerator
     * @param string $resourceClass
     * @param array $identifiers
     * @param string|null $operationName
     * @param array $context
     * @return void
     */
    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, ?string $operationName = null, array $context = [])
    {
    }
}
