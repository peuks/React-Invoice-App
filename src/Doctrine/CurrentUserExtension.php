<?php
// src/Doctrine/CurrentUserExtension.php
namespace App\Doctrine;

use App\Entity\User;
use App\Entity\Invoice;
use App\Entity\Customer;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;

/**
 * Permet de définir un utilisateur à une requête pour la liste des Invoices ou des Customers
 * Dans un cas un utilisateur est directement accessible ( un Customer a un user)
 * Dans l'autre cas, on utilise un join ( une invoice a un customer qui a un user)
 *
 * On prend en compte aussi le rôle admin pour pouvoir tout afficher.
 */
class CurrentUserExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    protected $security;

    /**
     * Permet de vérifier les authorisations des utilisateurs et d'accéder à la méthode isGranted()
     * @var [type]
     */
    protected  $auth;

    public function __construct(Security $security, AuthorizationCheckerInterface $auth)
    {
        $this->security = $security;
        $this->auth = $auth;
    }

    /**
     * Si l'on demande des invoices ou des customers alors agir sur la requête pour qu'elle tienne compte de l'utilisateur
     * Est ce que la ressource class ( la class qui nous intéresse c'est des invoices ou des customers)
     * cela revient à faire SELECT o from \App\Entity\Invoice AS o.
     *
     * A cela on veut rajouter quelque chose du genre WHERE o.user = :user
     *
     */
    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass)
    {
        dd($test);
        // Obtenir l'utilisateur connecté
        $user =  $this->security->getUser();

        if (
            // Checker qu'on a la bonne ressource
            ($resourceClass === Customer::class || $resourceClass === Invoice::class)
            // Que l'utilisateur à un Rôle Admin
            && !$this->auth->isGranted('ROLE_ADMIN')
            // Qu'il est connecté ( si pas connecté $user renvoie null )
            && $user instanceof User
        ) {

            /**
             *  Récupérer les listes des alias et selectionner que le premier
             *
             *  Si on récupère Customer et Invoice alors ils seront accessible sous forme d'alias
             *
             *  eg: La lettre o pour les invoices  et ou p pour les Customers
             *  @return Array
             */
            $rootAlias = $queryBuilder->getRootAliases()[0];

            switch ($resourceClass) {

                case Customer::class:
                    // Un Customer a directement l'id d'un user.
                    $queryBuilder
                        ->andWhere("$rootAlias.user:user = :user");
                    break;

                case Invoice::class:

                    // Invoice n'est pas ratachée directement à user mais a customer !
                    // Il faut d'abord faire une jointure sur Customer pour avoir accès au user
                    // Un $rootAlias.customer est accessible via l'alias "c"

                    $queryBuilder
                        ->join("$rootAlias.customer", "c")
                        ->andWhere("c.user = :user");
                    break;
            }
            // Dans les deux cas on a un paramètre :user qu'il faut définir
            $queryBuilder->setParameter("user", $user);
        }
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
        $this->addWhere($queryBuilder, $resourceClass);
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
        $this->addWhere($queryBuilder, $resourceClass);
    }
}
