<?php

/**
 * Copyright (c) 2019 BitPay
 **/

declare(strict_types=1);

namespace App\Service\Invoice\Update;

use App\Configuration\BitPayConfigurationInterface;
use App\Exception\MissingEntity;
use App\Exception\SignatureVerificationFailed;
use App\Factory\BitPayClientFactory;
use App\Repository\Invoice\InvoiceRepositoryInterface;
use App\Service\Invoice\BitPayInvoiceToEntityConverter;
use App\Service\Shared\Logger;

class UpdateInvoiceUsingBitPayIpn
{
    private InvoiceRepositoryInterface $invoiceRepository;
    private BitPayClientFactory $bitPayClientFactory;
    private BitPayConfigurationInterface $bitPayConfiguration;
    private Logger $logger;
    private BitPayInvoiceToEntityConverter $bitPayInvoiceToEntityConverter;
    private SendUpdateInvoiceEventStreamNotification $sendUpdateInvoiceNotification;
    private BitPayIpnValidator $bitPayIpnValidator;

    public function __construct(
        InvoiceRepositoryInterface $invoiceRepository,
        BitPayClientFactory $bitPayClientFactory,
        BitPayConfigurationInterface $bitPayConfiguration,
        BitPayInvoiceToEntityConverter $bitPayInvoiceToEntityConverter,
        SendUpdateInvoiceEventStreamNotification $sendUpdateInvoiceEventStreamNotification,
        Logger $logger,
        BitPayIpnValidator $bitPayIpnValidator
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->bitPayClientFactory = $bitPayClientFactory;
        $this->bitPayConfiguration = $bitPayConfiguration;
        $this->sendUpdateInvoiceNotification = $sendUpdateInvoiceEventStreamNotification;
        $this->logger = $logger;
        $this->bitPayInvoiceToEntityConverter = $bitPayInvoiceToEntityConverter;
        $this->bitPayIpnValidator = $bitPayIpnValidator;
    }

    /**
     * @param string $uuid
     */
    public function byUuid(string $uuid, array $data, array $headers): void
    {
        $invoice = $this->invoiceRepository->findOneByUuid($uuid);
        if (!$invoice) {
            throw new MissingEntity('Missing invoice');
        }

        $event = $data['event']['name'] ?? null;

        try {
            $client = $this->bitPayClientFactory->create();
            $bitPayId = $invoice->getBitpayId();
            $bitPayInvoice = $client->getInvoice(
                $bitPayId,
                $this->bitPayConfiguration->getFacade(),
                $this->bitPayConfiguration->isSignRequest()
            );
            if (!$bitPayInvoice->getId()) {
                throw new \InvalidArgumentException('Missing invoice in BitPay with BitPayId ' . $bitPayId);
            }

            $this->bitPayIpnValidator->execute($data, $headers);
            $invoice = $this->bitPayInvoiceToEntityConverter->execute($bitPayInvoice, $uuid);

            $this->logger->info('IPN_VALIDATE_SUCCESS', 'Successfully validated IP', ['bitpay_id' => $bitPayId]);

            $this->invoiceRepository->save($invoice, true);

            $this->logger->info('INVOICE_UPDATE_SUCCESS', 'Successfully updated invoice', [
                'id' => $invoice->getId()
            ]);
            $this->sendUpdateInvoiceNotification->execute($invoice, $event);
        } catch (SignatureVerificationFailed $e) {
            $this->logger->error('SIGNATURE_VERIFICATION_FAIL', 'Failed to verify signature', [
                'uuid' => $uuid
            ]);
            throw $e;
        } catch (MissingEntity | \InvalidArgumentException $e) {
            $this->logger->error('INVOICE_UPDATE_FAIL', 'Failed to update invoice', [
                'uuid' => $uuid
            ]);
            throw new \RuntimeException($e->getMessage());
        } catch (\Exception | \TypeError $e) {
            $this->logger->error('INVOICE_UPDATE_FAIL', 'Failed to update invoice', [
                'id' => $invoice->getId()
            ]);
            throw new \RuntimeException($e->getMessage());
        }
    }
}
