<?php

/**
 * Copyright (c) 2019 BitPay
 **/

declare(strict_types=1);

namespace App\Tests\Integration\Controller;

use App\Configuration\BitPayConfiguration;
use App\Tests\ExampleInvoice;
use App\Tests\ExampleSdkInvoice;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\Invoice\InvoiceRepositoryInterface;
use App\Factory\BitPayClientFactory;
use BitPaySDK\Client;
use App\Service\Invoice\Update\SendMercureUpdateInvoiceEventStream;
use BitPaySDK\Model\Invoice\Invoice;

class UpdateInvoiceTest extends WebTestCase
{
    /**
     * @test
     * @throws \JsonException
     */
    public function it_should_throws_404_for_update_invoice_with_non_existing_uuid(): void
    {
        $json = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'updateInvoice.json');
        $client = static::createClient();

        $client->request(
            'POST',
            '/invoices/non-existing-uuid',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $json
        );
        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     * @throws \JsonException
     */
    public function it_should_update_invoice_with_bitpay_ipn(): void
    {
        $json = json_encode(json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'updateInvoice.json'), true));
        $client = static::createClient();
        $container = static::getContainer();
        /** @var InvoiceRepositoryInterface $invoiceRepository */
        $invoiceRepository = $container->get(InvoiceRepositoryInterface::class);
        $invoice = ExampleInvoice::create();
        $invoiceRepository->save($invoice, true);

        $bitPayClientMock = $this->createMock(Client::class);
        $bitPayClientMock->method('getInvoice')->willReturn(ExampleSdkInvoice::create());

        $bitPayClientFactoryMock = $this->createMock(BitPayClientFactory::class);
        $bitPayClientFactoryMock->method('create')->willReturn($bitPayClientMock);

        $container->set(BitPayClientFactory::class, $bitPayClientFactoryMock);

        /** @var BitPayConfiguration $bitpayconfiguration */
        $bitPayConfiguration = $container->get(id: BitPayConfiguration::class);
        $token = $bitPayConfiguration->getToken();
        $signature = $this->getSignature($json, $token);

        $sendMercureUpdateInvoiceEventStreamMock = $this->createMock(SendMercureUpdateInvoiceEventStream::class);
        $sendMercureUpdateInvoiceEventStreamMock->expects($this->once())->method('execute');

        $container->set(SendMercureUpdateInvoiceEventStream::class, $sendMercureUpdateInvoiceEventStreamMock);

        $client->request(
            'POST',
            '/invoices/' . $invoice->getUuid(),
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
                'HTTP_X-SIGNATURE' => $signature,
            ],
            $json,
        );

        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    private function getSignature(string $data, string $token): string
    {
        return base64_encode(hash_hmac(
            'sha256',
            $data,
            $token,
            true
        ));
    }
}
