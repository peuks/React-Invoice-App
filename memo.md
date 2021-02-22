# Installing API Platform Core

```zsh
composer require api
```

Dans toutes mes entités je dois mettre.
Si une entité est liée à une autre, il faut aussi renseigner @ApiResource 

```php

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ApiResource
 */
class User implements UserInterface
```

Définir perPage pour des entités


```php
/**
 * @ORM\Entity(repositoryClass=InvoiceRepository::class)
 * @ApiResource(
 * attributes={
 *      "pagination_enabled"=true,
 *      "item_per_page"=20
 *  }
 * )
 * 
 */
```
##  Normalisation et Sérialisation

**Normalisation**
    
    Passage d'objet PHP en Array classique.

**Sérialisation**

    Passage des tableaux php et les transformze dans un autre format ( **eg** Array Json) . Du tableau php on peut *dénormaliser* a des entités doctrines.

## #Deux contextes possibles

![2 contextes possibles](memo_1.png)

De doctrine vers Json et react.

de react , via un formulaire à doctrine

![2 contextes possibles](memo_2.png)

### Normalisation
Il faut tout d'abord créer un group sur class puis spécificier sur chaque variable le groupe en question pour qu'il soit accessible.

```php
/**
 * @ORM\Entity(repositoryClass=InvoiceRepository::class)
 * @ApiResource(
 * normalizationContext={"groups"={"invoices_read"}}
 * )
 *
 * Ordonner la propriété X en ASC ou DESC
 *  exemple: /api/invoices?order[amount]=asc
 * @ApiFilter(OrderFilter::class,properties={"amount","sentAt"})
 * 
 */
```

```php
/**
 * @ORM\Column(type="string", length=255)
 * @Groups({"customers_read","invoices_read"})
 */
private $firstName;
```

## Activer / Désactiver des opérations sur une ressource

Par défaut 5 opérations sont possible 

Create
Read ( all and one)
U (update)
D (delete)
Il suffit de définir les options avec 
```php
/**
 * @ORM\Entity(repositoryClass=CustomerRepository::class)
 * @ApiResource(collectionOperations={"GET"={"path"="customers"},"POST"})
 */
```

## Une sous ressource : Récupérer les factures d'un client via l'url

Pour chaque ressource il est possible de créer une sous ressource.
