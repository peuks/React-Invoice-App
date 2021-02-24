<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\InvoiceRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;


/**
 * @ORM\Entity(repositoryClass=InvoiceRepository::class)
 * @ApiResource(
 * itemOperations={"GET","PUT","DELETE","increment"={
 *      "method"="post",
 *      "path"="/invoices/{id}/increment",
 *      "controller"="App\Controller\InvokeIncrementationController",
 *      "openapi_context"={
 *              "summary"="Incrémentation d'une facture",
 *              "description"="Incrémente le chrono d'une facture donnée"
 *       }
 *      }
 *  },
 * subresourceOperations={
 *  "api_customers_invoices_get_subresource"={
 *          "normalization_context"={"groups"={"invoices_subresource"}}
 *      }
 *  },
 * attributes={
 *      "pagination_enabled"=true,
 *      "pagination_items_per_page"=20,
 *      "order"={"sentAt":"desc"}
 *  },
 *  normalizationContext={"groups"={"invoices_read"}},
 *  denormalizationContext={"disable_type_enforcement"=true} 
 * )
 * @ApiFilter(OrderFilter::class,properties={"amount","sentAt"})
 * 
 */
class Invoice
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"invoices_read","customers_read","invoices_subresource"})
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     * @Groups({"invoices_read","customers_read","invoices_subresource"})
     * @Assert\NotBlank(message="Le montant de la facture est obligatoire")
     * @Assert\Type(type="numeric",message="Le montant doit être numérique.")
     */
    private $amount;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"invoices_read","customers_read","invoices_subresource"})
     * @Assert\Type(type="\DateTime",message="La date doir être au format YYYY-MM-DD")
     * @Assert\NotBlank(message="La date doit être renseignée")
     */
    private $sentAt;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"invoices_read","customers_read","invoices_subresource"})
     * @Assert\NotBlank(message="Le status de la facture est obligatoire")
     * @Assert\Choice(choices={"SEND","PAID","CANCELLED"},message="Les statuts possibles sont SEND, PAID et CANCELLED")
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=Customer::class, inversedBy="invoices")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"invoices_read"})
     * @Assert\NotBlank(message="Veuillez renseigner un customer pour cette facture")
     */
    private $customer;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"invoices_read","customers_read","invoices_subresource"})
     * @Assert\NotBlank(message="Il faut définir un chrono")
     * @Assert\Type(type="integer", message="Le chrono doit être un nombre !")
     */
    private $chrono;

    /**
     * Permet fde récupérer le User à qui apaprtient la facture
     * @Groups({"invoices_read","invoices_subresource"})
     * @return User|null
     */
    public function getUser(): User
    {
        return $this->customer->getUser();
    }

    /**
     *
     * @return integer|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    /**
     * Pour éviter l'erreur json, il faut autoriser n'importe quelle type de valeur.
     * La validation pourra ainsi être faite via les annotations.
     */
    public function setAmount($amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getSentAt(): ?\DateTimeInterface
    {
        return $this->sentAt;
    }

    public function setSentAt(\DateTimeInterface $sentAt): self
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getChrono(): ?int
    {
        return $this->chrono;
    }

    public function setChrono(int $chrono): self
    {
        $this->chrono = $chrono;

        return $this;
    }
}
