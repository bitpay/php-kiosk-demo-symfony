<?php

/**
 * Copyright (c) 2019 BitPay
 **/

declare(strict_types=1);

namespace App\Entity\Invoice;

use App\Repository\Invoice\InvoiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
#[ORM\Table(name: 'invoice')]
class Invoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?string $posDataJson;

    #[ORM\Column]
    private float $price;

    #[ORM\Column]
    private string $currencyCode;

    #[ORM\Column(nullable: true)]
    private ?string $bitpayId;

    #[ORM\Column]
    private string $status;

    #[ORM\Column]
    private \DateTimeImmutable $createdDate;

    #[ORM\Column(nullable: true)]
    private \DateTimeImmutable $expirationTime;

    #[ORM\Column(nullable: true)]
    private ?string $bitpayOrderId;

    #[ORM\Column(nullable: true)]
    private ?string $facadeType;

    #[ORM\Column(nullable: true)]
    private ?string $bitpayGuid;

    #[ORM\Column]
    private string $exceptionStatus = 'false';

    #[ORM\Column(nullable: true)]
    private ?string $bitpayUrl;

    #[ORM\Column(nullable: true)]
    private ?string $redirectUrl;

    #[ORM\Column(nullable: true)]
    private ?string $closeUrl;

    #[ORM\Column(nullable: true)]
    private ?int $acceptanceWindow;

    #[ORM\Column(nullable: true)]
    private ?string $token;

    #[ORM\Column(nullable: true)]
    private ?string $merchantName;

    #[ORM\Column(nullable: true)]
    private ?string $itemDescription;

    #[ORM\Column(nullable: true)]
    private ?string $billId;

    #[ORM\Column]
    private int $targetConfirmations = 0;

    #[ORM\Column]
    private bool $lowFeeDetected = false;

    #[ORM\Column]
    private bool $autoRedirect = false;

    #[ORM\Column(nullable: true)]
    private ?string $shopperUser;

    #[ORM\Column]
    private bool $jsonPayProRequired = false;

    #[ORM\Column]
    private bool $bitpayIdRequired = false;

    #[ORM\Column]
    private bool $isCancelled = false;

    #[ORM\Column(nullable: true)]
    private string $transactionSpeed;

    #[ORM\Column]
    private string $uuid;

    #[ORM\OneToOne(inversedBy: 'invoice', cascade: ['persist', 'remove'])]
    private ?Payment $payment = null;

    #[ORM\OneToOne(inversedBy: 'invoice', cascade: ['persist', 'remove'])]
    private ?Buyer $buyer = null;

    #[ORM\OneToOne(inversedBy: 'invoice', cascade: ['persist', 'remove'])]
    private ?Refund $refund = null;

    #[ORM\OneToMany(mappedBy: 'invoice', targetEntity: Transaction::class, orphanRemoval: true)]
    private Collection $transactions;

    #[ORM\OneToMany(mappedBy: 'invoice', targetEntity: ItemizedDetail::class, orphanRemoval: true)]
    private Collection $itemizedDetails;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
        $this->itemizedDetails = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getPosDataJson(): ?string
    {
        return $this->posDataJson;
    }

    /**
     * @param string|null $posDataJson
     */
    public function setPosDataJson(?string $posDataJson): void
    {
        $this->posDataJson = $posDataJson;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    /**
     * @return string
     */
    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    /**
     * @param string $currencyCode
     */
    public function setCurrencyCode(string $currencyCode): void
    {
        $this->currencyCode = $currencyCode;
    }

    /**
     * @return string|null
     */
    public function getBitpayId(): ?string
    {
        return $this->bitpayId;
    }

    /**
     * @param string|null $bitpayId
     */
    public function setBitpayId(?string $bitpayId): void
    {
        $this->bitpayId = $bitpayId;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedDate(): \DateTimeImmutable
    {
        return $this->createdDate;
    }

    /**
     * @param \DateTimeImmutable $createdDate
     */
    public function setCreatedDate(\DateTimeImmutable $createdDate): void
    {
        $this->createdDate = $createdDate;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getExpirationTime(): \DateTimeImmutable
    {
        return $this->expirationTime;
    }

    /**
     * @param \DateTimeImmutable $expirationTime
     */
    public function setExpirationTime(\DateTimeImmutable $expirationTime): void
    {
        $this->expirationTime = $expirationTime;
    }

    /**
     * @return string|null
     */
    public function getBitpayOrderId(): ?string
    {
        return $this->bitpayOrderId;
    }

    /**
     * @param string|null $bitpayOrderId
     */
    public function setBitpayOrderId(?string $bitpayOrderId): void
    {
        $this->bitpayOrderId = $bitpayOrderId;
    }

    /**
     * @return string|null
     */
    public function getFacadeType(): ?string
    {
        return $this->facadeType;
    }

    /**
     * @param string|null $facadeType
     */
    public function setFacadeType(?string $facadeType): void
    {
        $this->facadeType = $facadeType;
    }

    /**
     * @return string|null
     */
    public function getBitpayGuid(): ?string
    {
        return $this->bitpayGuid;
    }

    /**
     * @param string|null $bitpayGuid
     */
    public function setBitpayGuid(?string $bitpayGuid): void
    {
        $this->bitpayGuid = $bitpayGuid;
    }

    /**
     * @return bool
     */
    public function getExceptionStatus(): string
    {
        return $this->exceptionStatus;
    }

    /**
     * @param string $exceptionStatus
     */
    public function setExceptionStatus(string $exceptionStatus): void
    {
        $this->exceptionStatus = $exceptionStatus;
    }

    /**
     * @return string|null
     */
    public function getBitpayUrl(): ?string
    {
        return $this->bitpayUrl;
    }

    /**
     * @param string|null $bitpayUrl
     */
    public function setBitpayUrl(?string $bitpayUrl): void
    {
        $this->bitpayUrl = $bitpayUrl;
    }

    /**
     * @return string|null
     */
    public function getRedirectUrl(): ?string
    {
        return $this->redirectUrl;
    }

    /**
     * @param string|null $redirectUrl
     */
    public function setRedirectUrl(?string $redirectUrl): void
    {
        $this->redirectUrl = $redirectUrl;
    }

    /**
     * @return string|null
     */
    public function getCloseUrl(): ?string
    {
        return $this->closeUrl;
    }

    /**
     * @param string|null $closeUrl
     */
    public function setCloseUrl(?string $closeUrl): void
    {
        $this->closeUrl = $closeUrl;
    }

    /**
     * @return int|null
     */
    public function getAcceptanceWindow(): ?int
    {
        return $this->acceptanceWindow;
    }

    /**
     * @param int|null $acceptanceWindow
     */
    public function setAcceptanceWindow(?int $acceptanceWindow): void
    {
        $this->acceptanceWindow = $acceptanceWindow;
    }

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @param string|null $token
     */
    public function setToken(?string $token): void
    {
        $this->token = $token;
    }

    /**
     * @return string|null
     */
    public function getMerchantName(): ?string
    {
        return $this->merchantName;
    }

    /**
     * @param string|null $merchantName
     */
    public function setMerchantName(?string $merchantName): void
    {
        $this->merchantName = $merchantName;
    }

    /**
     * @return string|null
     */
    public function getItemDescription(): ?string
    {
        return $this->itemDescription;
    }

    /**
     * @param string|null $itemDescription
     */
    public function setItemDescription(?string $itemDescription): void
    {
        $this->itemDescription = $itemDescription;
    }

    /**
     * @return string|null
     */
    public function getBillId(): ?string
    {
        return $this->billId;
    }

    /**
     * @param string|null $billId
     */
    public function setBillId(?string $billId): void
    {
        $this->billId = $billId;
    }

    /**
     * @return int
     */
    public function getTargetConfirmations(): int
    {
        return $this->targetConfirmations;
    }

    /**
     * @param int $targetConfirmations
     */
    public function setTargetConfirmations(int $targetConfirmations): void
    {
        $this->targetConfirmations = $targetConfirmations;
    }

    /**
     * @return bool
     */
    public function isLowFeeDetected(): bool
    {
        return $this->lowFeeDetected;
    }

    /**
     * @param bool $lowFeeDetected
     */
    public function setLowFeeDetected(bool $lowFeeDetected): void
    {
        $this->lowFeeDetected = $lowFeeDetected;
    }

    /**
     * @return bool
     */
    public function isAutoRedirect(): bool
    {
        return $this->autoRedirect;
    }

    /**
     * @param bool $autoRedirect
     */
    public function setAutoRedirect(bool $autoRedirect): void
    {
        $this->autoRedirect = $autoRedirect;
    }

    /**
     * @return string|null
     */
    public function getShopperUser(): ?string
    {
        return $this->shopperUser;
    }

    /**
     * @param string|null $shopperUser
     */
    public function setShopperUser(?string $shopperUser): void
    {
        $this->shopperUser = $shopperUser;
    }

    /**
     * @return bool
     */
    public function isJsonPayProRequired(): bool
    {
        return $this->jsonPayProRequired;
    }

    /**
     * @param bool $jsonPayProRequired
     */
    public function setJsonPayProRequired(bool $jsonPayProRequired): void
    {
        $this->jsonPayProRequired = $jsonPayProRequired;
    }

    /**
     * @return bool
     */
    public function isBitpayIdRequired(): bool
    {
        return $this->bitpayIdRequired;
    }

    /**
     * @param bool $bitpayIdRequired
     */
    public function setBitpayIdRequired(bool $bitpayIdRequired): void
    {
        $this->bitpayIdRequired = $bitpayIdRequired;
    }

    /**
     * @return bool
     */
    public function isCancelled(): bool
    {
        return $this->isCancelled;
    }

    /**
     * @param bool $isCancelled
     */
    public function setIsCancelled(bool $isCancelled): void
    {
        $this->isCancelled = $isCancelled;
    }

    /**
     * @return string
     */
    public function getTransactionSpeed(): string
    {
        return $this->transactionSpeed;
    }

    /**
     * @param string $transactionSpeed
     */
    public function setTransactionSpeed(string $transactionSpeed): void
    {
        $this->transactionSpeed = $transactionSpeed;
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @param string $uuid
     */
    public function setUuid(string $uuid): void
    {
        $this->uuid = $uuid;
    }

    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    public function setPayment(?Payment $payment): void
    {
        $this->payment = $payment;
    }

    public function getBuyer(): ?Buyer
    {
        return $this->buyer;
    }

    public function setBuyer(?Buyer $buyer): void
    {
        $this->buyer = $buyer;
    }

    public function getRefund(): ?Refund
    {
        return $this->refund;
    }

    public function setRefund(?Refund $refund): void
    {
        $this->refund = $refund;
    }

    public function getTransactions(): ArrayCollection
    {
        return $this->transactions;
    }

    public function setTransactions(ArrayCollection $transactions): void
    {
        $this->transactions = $transactions;
    }

    /**
     * @return Collection<int, ItemizedDetail>
     */
    public function getItemizedDetails(): Collection
    {
        return $this->itemizedDetails;
    }

    public function addItemizedDetails(ItemizedDetail $itemizedDetails): void
    {
        if ($this->itemizedDetails->contains($itemizedDetails)) {
            return;
        }

        $this->itemizedDetails->add($itemizedDetails);
        $itemizedDetails->setInvoice($this);
    }

    public function removeItemizedDetails(ItemizedDetail $itemizedDetails): void
    {
        if (!$this->itemizedDetails->removeElement($itemizedDetails)) {
            return;
        }

        if ($itemizedDetails->getInvoice() === $this) {
            $itemizedDetails->setInvoice(null);
        }
    }

    public function addTransaction(Transaction $transaction): void
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);
            $transaction->setInvoice($this);
        }
    }

    public function removeTransaction(Transaction $transaction): void
    {
        if (!$this->transactions->removeElement($transaction)) {
            return;
        }

        if ($transaction->getInvoice() === $this) {
            $transaction->setInvoice(null);
        }
    }

    public function addItemizedDetail(ItemizedDetail $itemizedDetail): void
    {
        if (!$this->itemizedDetails->contains($itemizedDetail)) {
            $this->itemizedDetails->add($itemizedDetail);
            $itemizedDetail->setInvoice($this);
        }
    }

    public function removeItemizedDetail(ItemizedDetail $itemizedDetail): void
    {
        if (!$this->itemizedDetails->removeElement($itemizedDetail)) {
            return;
        }

        if ($itemizedDetail->getInvoice() === $this) {
            $itemizedDetail->setInvoice(null);
        }
    }
}
