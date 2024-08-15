<?php

/**
 * Copyright (c) 2019 BitPay
 **/

declare(strict_types=1);

namespace App\Tests\Unit\Service\Invoice\Create;

use App\Configuration\BitPayConfiguration;
use App\Configuration\Design;
use App\Configuration\Donation;
use App\Configuration\Field;
use App\Configuration\Hero;
use App\Configuration\Mode;
use App\Configuration\PosData;
use App\Entity\Invoice\Invoice;
use App\Factory\BitPayClientFactory;
use App\Factory\UuidFactory;
use App\Repository\Invoice\InvoiceRepositoryInterface;
use App\Service\Invoice\BitPayInvoiceToEntityConverter;
use App\Service\Invoice\Create\CreateInvoice;
use App\Service\Invoice\Create\DonationParamsValidator;
use App\Service\Invoice\Create\PosParamsValidator;
use App\Service\Shared\Logger;
use App\Service\Shared\UrlProvider;
use BitPaySDK\Client;
use BitPaySDK\Exceptions\BitPayException;
use BitPaySDK\Exceptions\BitPayGenericException;
use PHPUnit\Framework\TestCase;

class CreateInvoiceTest extends TestCase
{
    /**
     * @test
     */
    public function it_should_log_error_for_invalid_create_invoice_process(): void
    {
        $uuid = 'someUuid';
        $hero = $this->createStub(Hero::class);
        $priceField = new Field();
        $priceField->setType('price');
        $priceField->setRequired(true);
        $priceField->setName('price');
        $posData = new PosData();
        $posData->setFields([$priceField]);
        $design = new Design($hero, 'someLogo', $posData);
        $donation = $this->createMock(Donation::class);

        $bitPayConfiguration = new BitPayConfiguration(
            'pos',
            'test',
            $design,
            $donation,
            Mode::STANDARD,
            'someToken',
            'someNotification@email.com'
        );
        $bitPayClient = $this->createMock(Client::class);
        $bitPayClientFactory = $this->createMock(BitPayClientFactory::class);
        $uuidFactory = $this->createMock(UuidFactory::class);
        $urlProvider = $this->createMock(UrlProvider::class);
        $logger = $this->createMock(Logger::class);
        $createInvoice = new CreateInvoice(
            $bitPayConfiguration,
            $bitPayClientFactory,
            $this->createMock(BitPayInvoiceToEntityConverter::class),
            $this->createMock(InvoiceRepositoryInterface::class),
            new PosParamsValidator($bitPayConfiguration),
            $uuidFactory,
            $urlProvider,
            $logger
        );
        $params = [
            'store' => 'store-1',
            'register' => '2',
            'reg_transaction_no' => 'test123',
            'price' => '23.54'
        ];

        $urlProvider->method('applicationUrl')->willReturn('http://localhost');
        $uuidFactory->method('create')->willReturn($uuid);
        $bitPayClientFactory->method('create')->willReturn($bitPayClient);
        $bitPayClient->expects(self::once())->method('createInvoice')->willThrowException(new BitPayGenericException());
        $logger->expects(self::once())->method('error');
        $this->expectException(\RuntimeException::class);

        $createInvoice->execute($params);
    }

    /**
     * @test
     */
    public function it_should_create_standard_invoice(): void
    {
        $uuid = 'someUuid';
        $hero = $this->createStub(Hero::class);
        $priceField = new Field();
        $priceField->setType('price');
        $priceField->setRequired(true);
        $priceField->setName('price');
        $posData = new PosData();
        $posData->setFields([$priceField]);
        $design = new Design($hero, 'someLogo', $posData);
        $donation = $this->createMock(Donation::class);
        $bitPayConfiguration = new BitPayConfiguration(
            'pos',
            'test',
            $design,
            $donation,
            Mode::STANDARD,
            'someToken',
            'someNotification@email.com'
        );
        $bitPayClient = $this->createMock(Client::class);
        $bitPayClientFactory = $this->createMock(BitPayClientFactory::class);
        $uuidFactory = $this->createMock(UuidFactory::class);
        $urlProvider = $this->createMock(UrlProvider::class);
        $logger = $this->createMock(Logger::class);
        $bitPayInvoice = $this->createStub(\BitPaySDK\Model\Invoice\Invoice::class);
        $appInvoice = $this->createStub(Invoice::class);

        $urlProvider->method('applicationUrl')->willReturn('http://localhost');
        $uuidFactory->method('create')->willReturn($uuid);
        $bitPayClientFactory->method('create')->willReturn($bitPayClient);
        $bitPayClient->expects(self::once())->method('createInvoice')->willReturn($bitPayInvoice);

        $bitPayInvoiceToEntityConverter = $this->createMock(BitPayInvoiceToEntityConverter::class);
        $bitPayInvoiceToEntityConverter->expects(self::once())->method('execute')->with($bitPayInvoice, $uuid)
            ->willReturn($appInvoice);
        $logger->expects(self::once())->method('info');
        $invoiceRepository = $this->createMock(InvoiceRepositoryInterface::class);
        $invoiceRepository->expects(self::once())->method('save')->with($appInvoice, true);

        $createInvoice = new CreateInvoice(
            $bitPayConfiguration,
            $bitPayClientFactory,
            $bitPayInvoiceToEntityConverter,
            $invoiceRepository,
            new PosParamsValidator($bitPayConfiguration),
            $uuidFactory,
            $urlProvider,
            $logger
        );
        $params = [
            'store' => 'store-1',
            'register' => '2',
            'reg_transaction_no' => 'test123',
            'price' => '23.54'
        ];
        $result = $createInvoice->execute($params);
        self::assertEquals($appInvoice, $result);
    }

    /**
     * @test
     */
    public function it_should_create_donation_invoice(): void
    {
        $uuid = 'someUuid';
        $hero = $this->createStub(Hero::class);
        $priceField = new Field();
        $priceField->setType('price');
        $priceField->setRequired(true);
        $priceField->setName('price');
        $posData = new PosData();
        $posData->setFields([$priceField]);
        $design = new Design($hero, 'someLogo', $posData);
        $donation = $this->createMock(Donation::class);
        $bitPayConfiguration = new BitPayConfiguration(
            'pos',
            'test',
            $design,
            $donation,
            Mode::DONATION,
            'someToken',
            'someNotification@email.com'
        );
        $bitPayClient = $this->createMock(Client::class);
        $bitPayClientFactory = $this->createMock(BitPayClientFactory::class);
        $uuidFactory = $this->createMock(UuidFactory::class);
        $urlProvider = $this->createMock(UrlProvider::class);
        $logger = $this->createMock(Logger::class);
        $bitPayInvoice = $this->createStub(\BitPaySDK\Model\Invoice\Invoice::class);
        $appInvoice = $this->createStub(Invoice::class);
        $invoiceRepository = $this->createMock(InvoiceRepositoryInterface::class);

        $urlProvider->method('applicationUrl')->willReturn('http://localhost');
        $uuidFactory->method('create')->willReturn($uuid);
        $bitPayClientFactory->method('create')->willReturn($bitPayClient);
        $bitPayClient->expects(self::once())->method('createInvoice')->willReturn($bitPayInvoice);
        $bitPayInvoiceToEntityConverter = $this->createMock(BitPayInvoiceToEntityConverter::class);
        $bitPayInvoiceToEntityConverter->expects(self::once())->method('execute')->with($bitPayInvoice, $uuid)
            ->willReturn($appInvoice);
        $logger->expects(self::once())->method('info');

        $invoiceRepository->expects(self::once())->method('save')->with($appInvoice, true);

        $createInvoice = new CreateInvoice(
            $bitPayConfiguration,
            $bitPayClientFactory,
            $bitPayInvoiceToEntityConverter,
            $invoiceRepository,
            new DonationParamsValidator(new PosParamsValidator($bitPayConfiguration)),
            $uuidFactory,
            $urlProvider,
            $logger
        );
        $params = [
            'store' => 'store-1',
            'register' => '2',
            'reg_transaction_no' => 'test123',
            'price' => '23.54',
            'buyerName' => 'Test',
            'buyerAddress1' => 'SomeTestAddress',
            'buyerAddress2' => null,
            'buyerLocality' => 'SomeCity',
            'buyerRegion' => 'AK',
            'buyerPostalCode' => '12345',
            'buyerPhone' => '997',
            'buyerEmail' => 'some@email.com',
        ];
        $result = $createInvoice->execute($params);
        self::assertEquals($appInvoice, $result);
    }
}
