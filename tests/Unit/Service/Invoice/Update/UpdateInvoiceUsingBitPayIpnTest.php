<?php

/**
 * Copyright (c) 2019 BitPay
 **/

declare(strict_types=1);

namespace App\Tests\Unit\Service\Invoice\Update;

use App\Configuration\BitPayConfigurationInterface;
use App\Exception\MissingEntity;
use App\Factory\BitPayClientFactory;
use App\Repository\Invoice\InvoiceRepositoryInterface;
use App\Service\Invoice\BitPayInvoiceToEntityConverter;
use App\Service\Invoice\Update\BitPayIpnValidatorInterface;
use App\Service\Invoice\Update\SendUpdateInvoiceEventStreamNotification;
use App\Service\Invoice\Update\UpdateInvoiceUsingBitPayIpn;
use App\Service\Shared\Logger;
use App\Tests\ExampleInvoice;
use App\Tests\ExampleSdkInvoice;
use BitPaySDK\Model\Facade;
use BitPaySDK\PosClient;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class UpdateInvoiceUsingBitPayIpnTest extends TestCase
{
    #[Test]
    public function it_should_throws_exception_for_missing_invoice(): void
    {
        $this->expectException(MissingEntity::class);
        $repository = $this->getRepository();
        $repository->method('findOneByUuid')->willReturn(null);
        $ipnValidator = $this->createMock(BitPayIpnValidatorInterface::class);

        $testedClass = new UpdateInvoiceUsingBitPayIpn(
            $repository,
            $this->getClientFactory(),
            $this->getBitPayConfiguration(),
            $this->getBitPayInvoiceToEntityConverter(),
            $this->getSendUpdateInvoiceEventStream(),
            $this->getLogger(),
            $ipnValidator
        );
        $testedClass->byUuid('12312', [], []);
    }

    #[Test]
    public function it_should_update_invoice_using_bitpay_update_response(): void
    {
        // given
        $uuid = '12312';
        $repository = $this->getRepository();
        $bitPayClientFactory = $this->getClientFactory();
        $applicationInvoice = ExampleInvoice::create();
        $sdkInvoice = ExampleSdkInvoice::create();
        $bitPayConfiguration = $this->getBitPayConfiguration();
        $bitPayInvoiceToEntityConverter = $this->getBitPayInvoiceToEntityConverter();
        $sendUpdateInvoiceEventStreamNotification = $this->getSendUpdateInvoiceEventStream();
        $logger = $this->getLogger();
        $sdkClient = $this->createMock(PosClient::class);
        $bitPayClientFactory->expects(self::once())->method('create')->willReturn($sdkClient);
        $bitPayConfiguration->method('getFacade')->willReturn('pos');
        $bitPayConfiguration->method('isSignRequest')->willReturn(false);
        $ipnValidator = $this->createMock(BitPayIpnValidatorInterface::class);

        $testedClass = new UpdateInvoiceUsingBitPayIpn(
            $repository,
            $bitPayClientFactory,
            $bitPayConfiguration,
            $bitPayInvoiceToEntityConverter,
            $sendUpdateInvoiceEventStreamNotification,
            $logger,
            $ipnValidator
        );

        // then
        $sdkClient->expects(self::once())->method('getInvoice')
            ->with($applicationInvoice->getBitpayId(), Facade::POS, false)
            ->willReturn($sdkInvoice);
        $repository->expects(self::once())->method('findOneByUuid')->with($uuid)->willReturn($applicationInvoice);
        $bitPayInvoiceToEntityConverter->expects(self::once())->method('execute')->with($sdkInvoice, $uuid)
            ->willReturn($applicationInvoice);
        $repository->expects(self::once())->method('save')->with($applicationInvoice, true);
        $logger->expects(self::exactly(2))->method('info');
        $sendUpdateInvoiceEventStreamNotification->expects(self::once())->method('execute');

        // when
        $testedClass->byUuid($uuid, [], []);
    }

    private function getRepository(): InvoiceRepositoryInterface|MockObject
    {
        return $this->createMock(InvoiceRepositoryInterface::class);
    }

    private function getClientFactory(): MockObject|BitPayClientFactory
    {
        return $this->createMock(BitPayClientFactory::class);
    }

    private function getBitPayConfiguration(): MockObject|BitPayConfigurationInterface
    {
        return $this->createMock(BitPayConfigurationInterface::class);
    }

    private function getSendUpdateInvoiceEventStream(): SendUpdateInvoiceEventStreamNotification|MockObject
    {
        return $this->createMock(SendUpdateInvoiceEventStreamNotification::class);
    }

    private function getLogger(): Logger|MockObject
    {
        return $this->createMock(Logger::class);
    }

    private function getBitPayInvoiceToEntityConverter(): BitPayInvoiceToEntityConverter|MockObject
    {
        return $this->createMock(BitPayInvoiceToEntityConverter::class);
    }
}
