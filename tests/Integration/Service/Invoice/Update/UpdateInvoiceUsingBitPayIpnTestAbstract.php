<?php

/**
 * Copyright (c) 2019 BitPay
 **/

declare(strict_types=1);

namespace App\Tests\Integration\Service\Invoice\Update;

use App\Configuration\BitPayConfigurationFactoryInterface;
use App\Entity\Invoice\PaymentCurrency;
use App\Factory\BitPayClientFactory;
use App\Repository\Invoice\InvoiceRepositoryInterface;
use App\Service\Invoice\Update\UpdateInvoiceUsingBitPayIpn;
use App\Tests\ExampleInvoice;
use App\Tests\ExampleSdkInvoice;
use App\Tests\Integration\AbstractIntegrationTestCase;
use BitPaySDK\Client;
use BitPaySDK\Model\Facade;
use BitPaySDK\Model\Invoice\Invoice;
use BitPaySDK\PosClient;

class UpdateInvoiceUsingBitPayIpnTestAbstract extends AbstractIntegrationTestCase
{
    /**
     * @test
     * @throws \JsonException
     */
    public function it_should_not_update_invoice_for_invalid_bitpay_order_id(): void
    {
        // given
        $container = static::getContainer();
        /** @var InvoiceRepositoryInterface $invoiceRepository */
        $invoiceRepository = $container->get(InvoiceRepositoryInterface::class);
        $invoice = ExampleInvoice::create();
        $invoice->setBitpayOrderId('someInvalidId');
        $invoiceRepository->save($invoice, true);

        $uuid = $invoice->getUuid();

        $mock = $this->createMock(BitPayConfigurationFactoryInterface::class);
        $clientFactory = new class ($mock) extends BitPayClientFactory {
            public function create(): Client
            {
                return new class ('', '') extends PosClient {
                    public function getInvoice(
                        string $invoiceId,
                        string $facade = Facade::MERCHANT,
                        bool $signRequest = true
                    ): Invoice {
                        return new Invoice();
                    }
                };
            }
        };
        $container->set(BitPayClientFactory::class, $clientFactory);

        // assert
        $this->expectException(\RuntimeException::class);

        // when
        $this->getTestedClass()->byUuid($uuid, [], []);
    }

    /**
     * @test
     * @throws \JsonException
     */
    public function it_should_update_invoice(): void
    {
        // given
        $container = static::getContainer();
        /** @var InvoiceRepositoryInterface $invoiceRepository */
        $invoiceRepository = $container->get(InvoiceRepositoryInterface::class);
        $invoice = ExampleInvoice::create();
        $invoice->setBitpayOrderId('someInvalidId');
        $invoiceRepository->save($invoice, true);

        $uuid = $invoice->getUuid();

        $mock = $this->createMock(BitPayConfigurationFactoryInterface::class);
        $clientFactory = new class ($mock) extends BitPayClientFactory {
            public function create(): Client
            {
                return new class ('', '') extends PosClient {
                    public function getInvoice(
                        string $invoiceId,
                        string $facade = Facade::MERCHANT,
                        bool $signRequest = true
                    ): Invoice {
                        $paymentTotals = [
                            'BTC' => 200,
                        ];

                        $invoice = ExampleSdkInvoice::create();
                        $invoice->setId(ExampleInvoice::BITPAY_ID);
                        $invoice->setOrderId(ExampleInvoice::BITPAY_ORDER_ID);
                        $invoice->setPrice(10.2);
                        $invoice->setTransactionSpeed('low');
                        $invoice->setPaymentTotals($paymentTotals);
                        $invoice->getBuyerProvidedInfo()->setName('changed');
                        $invoice->setStatus('expired');
                        $invoice->setUrl('https://test.bitpay.com/invoice?id=MV9fy5iNDkqrg4qrfYpw1h');
                        $invoice->setAmountPaid(4);

                        return $invoice;
                    }
                };
            }
        };
        $container->set(BitPayClientFactory::class, $clientFactory);

        // when
        $this->getTestedClass()->byUuid($uuid, [], []);
        /** @var \App\Entity\Invoice\Invoice $invoice */
        $invoice = $invoiceRepository->findOneByUuid($uuid);

        self::assertEquals(10.2, $invoice->getPrice());
        self::assertEquals('low', $invoice->getTransactionSpeed());
        $btcTotal = null;
        /** @var PaymentCurrency $paymentCurrency */
        foreach ($invoice->getPayment()->getCurrencies() as $paymentCurrency) {
            $currencyCode = $paymentCurrency->getCurrencyCode();
            if ($currencyCode === 'BTC') {
                $btcTotal = $paymentCurrency->getTotal();
            }
        }
        self::assertEquals(200, $btcTotal);
        self::assertEquals('changed', $invoice->getBuyer()->getProvidedInfo()->getName());
        self::assertEquals('expired', $invoice->getStatus());
        self::assertEquals(
            'https://test.bitpay.com/invoice?id=MV9fy5iNDkqrg4qrfYpw1h',
            $invoice->getBitpayUrl()
        );
        self::assertEquals(false, $invoice->getExceptionStatus());
        self::assertEquals(4, $invoice->getPayment()->getAmountPaid());
    }

    private function getTestedClass(): UpdateInvoiceUsingBitPayIpn
    {
        $container = static::getContainer();

        return $container->get(UpdateInvoiceUsingBitPayIpn::class);
    }
}
