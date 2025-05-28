<?php

/**
 * Copyright (c) 2019 BitPay
 **/

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Invoice;

use App\Configuration\BitPayConfigurationInterface;
use App\Configuration\Mode;
use App\Entity\Invoice\Invoice;
use App\Repository\Invoice\InvoiceRepositoryInterface;
use App\Tests\Functional\AbstractFunctionalTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CreateInvoiceTest extends AbstractFunctionalTestCase
{
    /**
     * @test
     */
    public function it_should_fill_form_and_create_standard_bitpay_invoice(): void
    {
        /** @var BitPayConfigurationInterface $configuration */
        $client = static::createClient();
        $container = static::getContainer();
        $configuration = $container->get(BitPayConfigurationInterface::class);
        $configuration->setMode(Mode::STANDARD);

        $client->request(
            Request::METHOD_POST,
            '/invoices',
            [
                'store' => 'store-1',
                'register' => '2',
                'reg_transaction_no' => 'test123',
                'price' => '23.54'
            ],
        );

        $this->validateResponse($client->getResponse());

        /** @var InvoiceRepositoryInterface $invoiceRepository */
        $invoiceRepository = $container->get(InvoiceRepositoryInterface::class);
        /** @var Invoice $invoice */
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

    /**
     * @test
     */
    public function it_should_fill_form_and_create_donation_bitpay_invoice(): void
    {
        $client = static::createClient();
        $container = static::getContainer();
        /** @var BitPayConfigurationInterface $configuration */
        $configuration = $container->get(BitPayConfigurationInterface::class);
        $configuration->setMode(Mode::DONATION);

        $expectedName = 'Test';
        $expectedPhone = '997';
        $client->request(
            Request::METHOD_POST,
            '/invoices',
            [
                'buyerName' => $expectedName,
                'buyerAddress1' => 'SomeTestAddress',
                'buyerAddress2' => null,
                'buyerLocality' => 'SomeCity',
                'buyerRegion' => 'AK',
                'buyerPostalCode' => '12345',
                'buyerPhone' => $expectedPhone,
                'buyerEmail' => 'some@email.com',
                '_token' => 'FsBmW4CMKpw8zjEBO9F3TQu9tGBbcTJkPQPKcGv7',
                'store' => 'store-1',
                'register' => '2',
                'reg_transaction_no' => 'test123',
                'price' => '23.54'
            ]
        );

        $this->validateResponse($client->getResponse());

        /** @var InvoiceRepositoryInterface $invoiceRepository */
        $invoiceRepository = $container->get(InvoiceRepositoryInterface::class);
        /** @var Invoice $invoice */
        $invoice = $invoiceRepository->find(1);
        $invoiceBuyerProvidedInfo = $invoice->getBuyer()->getProvidedInfo();

        self::assertNotNull($invoice);
        self::assertNotNull($invoice->getBitpayId());
        self::assertNotNull($invoice->getBitpayOrderId());
        // @codingStandardsIgnoreStart
        self::assertEquals(
            '{"store":"store-1","register":"2","reg_transaction_no":"test123","price":"23.54","buyerName":"Test","buyerAddress1":"SomeTestAddress","buyerAddress2":null,"buyerLocality":"SomeCity","buyerRegion":"AK","buyerPostalCode":"12345","buyerPhone":"997","buyerEmail":"some@email.com"}',
            $invoice->getPosDataJson()
        );
        // @codingStandardsIgnoreEnd
        self::assertEquals(23.54, $invoice->getPrice());
        self::assertEquals($expectedName, $invoiceBuyerProvidedInfo->getName());
        self::assertEquals($expectedPhone, $invoiceBuyerProvidedInfo->getPhoneNumber());
    }

    private function validateResponse(Response $response): void
    {
        if ($response->getStatusCode() === Response::HTTP_INTERNAL_SERVER_ERROR) {
            $docsUrl = 'https://developer.bitpay.com/docs/signed-request-flow#request-generate-token';
            throw new \Exception(
                'Configuration error. The functional test requires a valid BitPay token.' .
                'Please refer to ' . $docsUrl . ' for more information.'
            );
        }
    }
}
