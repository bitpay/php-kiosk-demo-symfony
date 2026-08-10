<?php

/**
 * Copyright (c) 2019 BitPay
 **/

declare(strict_types=1);

namespace App\Tests\Integration\Controller;

use App\Repository\Invoice\InvoiceRepositoryInterface;
use App\Tests\ExampleInvoice;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class GetInvoicesTest extends WebTestCase
{
    #[Test]
    public function it_should_show_invoices_on_grid(): void
    {
        $client = static::createClient();
        $container = static::getContainer();
        /** @var InvoiceRepositoryInterface $invoiceRepository */
        $invoiceRepository = $container->get(InvoiceRepositoryInterface::class);
        $invoice = ExampleInvoice::create();
        $invoiceRepository->save($invoice, true);

        $client->request(Request::METHOD_GET, '/invoices/');
        $client->followRedirect();
        $content = $client->getResponse()->getContent();

        self::assertStringContainsString(ExampleInvoice::BITPAY_ID, $content);
        self::assertStringContainsString((string) $invoice->getPrice(), $content);
        self::assertStringContainsString($invoice->getItemDescription(), $content);
        self::assertStringContainsString($invoice->getStatus(), $content);
    }
}
