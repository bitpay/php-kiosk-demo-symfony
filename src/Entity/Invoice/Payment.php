<?php

namespace App\Entity\Invoice;

use App\Repository\Invoice\PaymentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
#[ORM\Table(name: 'invoice_payment')]
class Payment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?float $amountPaid = null;

    #[ORM\Column(nullable: true)]
    private ?string $displayAmountPaid = null;

    #[ORM\Column(nullable: true)]
    private ?int $underpaidAmount = null;

    #[ORM\Column(nullable: true)]
    private ?int $overpaidAmount = null;

    #[ORM\Column]
    private bool $nonPayProPaymentReceived = false;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $transactionCurrency = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $universalCodesPaymentString = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $universalCodesVerificationLink = null;

    #[ORM\OneToOne(mappedBy: 'payment', cascade: ['persist'])]
    private ?Invoice $invoice = null;

    #[ORM\OneToMany(
        mappedBy: 'payment',
        targetEntity: PaymentCurrency::class,
        cascade: ['persist'],
        orphanRemoval: true
    )]
    private Collection $currencies;

    public function __construct()
    {
        $this->currencies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmountPaid(): ?float
    {
        return $this->amountPaid;
    }

    public function setAmountPaid(?float $amountPaid): void
    {
        $this->amountPaid = $amountPaid;
    }

    public function getDisplayAmountPaid(): ?float
    {
        return $this->displayAmountPaid;
    }

    public function setDisplayAmountPaid(?float $displayAmountPaid): void
    {
        $this->displayAmountPaid = $displayAmountPaid;
    }

    public function getUnderpaidAmount(): ?int
    {
        return $this->underpaidAmount;
    }

    public function setUnderpaidAmount(?int $underpaidAmount): void
    {
        $this->underpaidAmount = $underpaidAmount;
    }

    public function getOverpaidAmount(): ?int
    {
        return $this->overpaidAmount;
    }

    public function setOverpaidAmount(?int $overpaidAmount): void
    {
        $this->overpaidAmount = $overpaidAmount;
    }

    public function isNonPayProPaymentReceived(): ?bool
    {
        return $this->nonPayProPaymentReceived;
    }

    public function setNonPayProPaymentReceived(bool $nonPayProPaymentReceived): void
    {
        $this->nonPayProPaymentReceived = $nonPayProPaymentReceived;
    }

    public function getTransactionCurrency(): ?string
    {
        return $this->transactionCurrency;
    }

    public function setTransactionCurrency(?string $transactionCurrency): void
    {
        $this->transactionCurrency = $transactionCurrency;
    }

    public function getUniversalCodesPaymentString(): ?string
    {
        return $this->universalCodesPaymentString;
    }

    public function setUniversalCodesPaymentString(?string $universalCodesPaymentString): void
    {
        $this->universalCodesPaymentString = $universalCodesPaymentString;
    }

    public function getUniversalCodesVerificationLink(): ?string
    {
        return $this->universalCodesVerificationLink;
    }

    public function setUniversalCodesVerificationLink(?string $universalCodesVerificationLink): void
    {
        $this->universalCodesVerificationLink = $universalCodesVerificationLink;
    }

    public function getInvoice(): ?Invoice
    {
        return $this->invoice;
    }

    public function setInvoice(?Invoice $invoice): void
    {
        if ($invoice === null && $this->invoice !== null) {
            $this->invoice->setPayment(null);
        }

        if ($invoice !== null && $invoice->getPayment() !== $this) {
            $invoice->setPayment($this);
        }

        $this->invoice = $invoice;
    }

    /**
     * @return Collection<int, PaymentCurrency>
     */
    public function getCurrencies(): Collection
    {
        return $this->currencies;
    }

    public function addCurrency(PaymentCurrency $currency): void
    {
        if (!$this->currencies->contains($currency)) {
            $this->currencies->add($currency);
            $currency->setPayment($this);
        }
    }

    public function removeCurrency(PaymentCurrency $currency): void
    {
        if (!$this->currencies->removeElement($currency)) {
            return;
        }

        if ($currency->getPayment() === $this) {
            $currency->setPayment(null);
        }
    }
}
