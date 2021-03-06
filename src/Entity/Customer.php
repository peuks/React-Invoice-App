<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CustomerRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=CustomerRepository::class)
 * @ApiResource(
 *  collectionOperations={"GET"={"path"="/customers"},"POST"},
 *  itemOperations={"GET"={"path"="/customers/{id}"},"PUT","DELETE"},
 *  subresourceOperations={
 *      "invoices_get_subresource"={
 *          "path"="/customers/{id}/invoices"
 *      }
 *  },
 *  normalizationContext={
 *      "groups"={"customers_read"}
 *  } 
 * )
 * @ApiFilter(SearchFilter::class,properties={"firstName":"partial","lastName":"partial","company":"partial"})
 * @ApiFilter(OrderFilter::class)
 *
 */
class Customer
{
    /**
     * @ORM\Id 
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"customers_read","invoices_read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"customers_read","invoices_read"})
     * @Assert\NotBlank(message="Le prénom du client est obligatoire")
     * @Assert\Length(min=3,minMessage="Le prénom doit faire plus de {{ limit }} caractères",max=50,maxMessage="Le prénom doit contenir moins de {{ limit }} caractères")
     * 
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"customers_read","invoices_read"})
     * @Assert\NotBlank(message="Le nom de famille est obligatoire")
     * @Assert\Length(min=3,minMessage="Le nom de famille doit faire plus de {{ limit }} caractères",max=50,maxMessage="Le nom de famille doit contenir moins de {{ limit }} caractères")
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"customers_read","invoices_read"})
     * @Assert\NotBlank(message="L'adresse email du customer est obligatoire")
     * @Assert\Email(message="Le format de l'adresse email est incorrecte")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"customers_read","invoices_read"})
     */
    private $company;

    /**
     * @ORM\OneToMany(targetEntity=Invoice::class, mappedBy="customer")
     * @Groups({"customers_read"})
     * @ApiSubresource
     */
    private $invoices;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="customer")
     * @Groups({"customers_read"})
     * @Assert\NotBlank(message="L'utilisateur est obligatoire")
     */
    private $user;

    public function __construct()
    {
        $this->invoices = new ArrayCollection();
    }

    /**
     * Will return the total amount of invoices
     * @Groups({"customers_read"}) 
     * 
     * @return float
     */
    public function getTotalAmount(): float
    {
        // Transform arraycollection to array
        return array_reduce($this->invoices->toArray(), function ($total, $invoce) {
            return $total + $invoce->getAmount();
        }, 0);
    }

    /**
     * Return the total amount of unpaid invoices
     * @Groups({"customers_read"}) 
     * @return float
     */
    public function getTotalUnpaid(): float
    {
        // Transform arraycollection to array
        return array_reduce($this->invoices->toArray(), function ($total, $invoce) {
            // Get total of invoices unpaid
            return $total + ($invoce->getStatus() === "PAID" || $invoce->getStatus() === "CANCELLED") ? 0 : $invoce->getAmount();
        }, 0);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstname(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): self
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return Collection|Invoice[]
     */
    public function getInvoices(): Collection
    {
        return $this->invoices;
    }

    public function addInvoice(Invoice $invoice): self
    {
        if (!$this->invoices->contains($invoice)) {
            $this->invoices[] = $invoice;
            $invoice->setCustomer($this);
        }

        return $this;
    }

    public function removeInvoice(Invoice $invoice): self
    {
        if ($this->invoices->removeElement($invoice)) {
            // set the owning side to null (unless already changed)
            if ($invoice->getCustomer() === $this) {
                $invoice->setCustomer(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
