<?php

/**
 * Copyright (c) 2019 BitPay
 **/

declare(strict_types=1);

namespace App\Tests\Integration\Service\Invoice\Create;

use App\Configuration\BitPayConfigurationFactoryInterface;
use App\Factory\BitPayClientFactory;
use App\Repository\Invoice\InvoiceRepository;
use App\Service\Invoice\Create\CreateInvoice;
use App\Tests\Integration\AbstractIntegrationTestCase;
use BitPaySDK\Client;
use BitPaySDK\Model\Facade;
use BitPaySDK\Model\Invoice\Invoice;
use BitPaySDK\PosClient;
use App\Tests\ExampleSdkInvoice;

class CreateInvoiceTestAbstract extends AbstractIntegrationTestCase
{
    /**
     * @test
     */
    public function it_should_create_kiosk_invoice(): void
    {
        $container = static::getContainer();
        $mock = $this->createMock(BitPayConfigurationFactoryInterface::class);

        $clientFactory = new class ($mock) extends BitPayClientFactory {
            public function create(): Client
            {
                return new class ('', '') extends PosClient {
                    public function createInvoice(
                        Invoice $invoice,
                        string $facade = Facade::MERCHANT,
                        bool $signRequest = true
                    ): Invoice {
                        return ExampleSdkInvoice::create();
                    }

                    public function getInvoice(
                        string $invoiceId,
                        string $facade = Facade::MERCHANT,
                        bool $signRequest = true
                    ): Invoice {
                        return ExampleSdkInvoice::create();
                    }
                };
            }
        };
        $container->set(BitPayClientFactory::class, $clientFactory);

        $testedClass = $this->getTestedClass();
        $testedClass->execute([
            '_token' => 'FsBmW4CMKpw8zjEBO9F3TQu9tGBbcTJkPQPKcGv7',
            'store' => 'store-1',
            'register' => '2',
            'reg_transaction_no' => 'test123',
            'price' => 23.54
        ]);

        /** @var InvoiceRepository $invoiceRepository */
        $invoiceRepository = $container->get(InvoiceRepository::class);
        /** @var \App\Entity\Invoice\Invoice $invoice */
        $invoice = $invoiceRepository->find(1);

        self::assertNotNull($invoice);
        self::assertNotNull($invoice->getBitpayId());
        self::assertNotNull($invoice->getBitpayOrderId());
        self::assertEquals(
            '{"store":"store-1","register":"2","reg_transaction_no":"test123","price":"23.54"}',
            $invoice->getPosDataJson()
        );
        self::assertEquals(23.54, $invoice->getPrice());
    }

    private function getTestedClass(): CreateInvoice
    {
        $container = static::getContainer();

        return $container->get(CreateInvoice::class);
    }
}
