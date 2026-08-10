<?php

/**
 * Copyright (c) 2019 BitPay
 **/

declare(strict_types=1);

namespace App\Tests\Unit\Configuration;

use App\Configuration\BitPayConfiguration;
use App\Configuration\Design;
use App\Configuration\Donation;
use App\Configuration\Field;
use App\Configuration\Mode;
use App\Configuration\PosData;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class BitPayConfigurationTest extends TestCase
{
    private const TOKEN = 'someToken';
    private const ENV = 'prod';
    private const FACADE = 'merchant';
    private const NOTIFICATION_EMAIL = 'some@email.com';

    #[Test]
    public function it_should_return_environemnt(): void
    {
        $testedClass = $this->getTestedClass();
        self::assertEquals(self::ENV, $testedClass->getEnvironment());
    }

    #[Test]
    public function it_should_return_facade(): void
    {
        $testedClass = $this->getTestedClass();
        self::assertEquals(self::FACADE, $testedClass->getFacade());
    }

    #[Test]
    public function it_should_return_design(): void
    {
        $design = $this->getDesign();
        $testedClass = $this->getTestedClass($design);
        self::assertEquals($design, $testedClass->getDesign());
    }

    #[Test]
    public function it_should_return_token(): void
    {
        $testedClass = $this->getTestedClass();
        self::assertEquals(self::TOKEN, $testedClass->getToken());
    }

    #[Test]
    public function it_should_return_notification_email(): void
    {
        $testedClass = $this->getTestedClass();
        self::assertEquals(self::NOTIFICATION_EMAIL, $testedClass->getNotificationEmail());
    }

    #[Test]
    public function it_should_return_is_sign_request(): void
    {
        $testedClass = new BitPayConfiguration(
            'pos',
            self::ENV,
            $this->getDesign(),
            $this->getDonation(),
            Mode::STANDARD,
            null,
            null
        );
        self::assertEquals(false, $testedClass->isSignRequest());

        $testedClass = new BitPayConfiguration(
            'merchant',
            self::ENV,
            $this->getDesign(),
            $this->getDonation(),
            Mode::STANDARD,
            null,
            null
        );
        self::assertEquals(true, $testedClass->isSignRequest());
    }

    #[Test]
    public function it_should_return_iso_code(): void
    {
        // given
        $field1 = new Field();
        $field1->setType('someType');
        $field2 = new Field();
        $field2->setType('price');
        $currency = 'PLN';
        $field2->setCurrency($currency);

        $posData = $this->createStub(PosData::class);
        $posData->method('getFields')->willReturn([
            $field1,
            $field2
        ]);
        $design = $this->createStub(Design::class);
        $design->method('getPosData')->willReturn($posData);

        $testedClass = new BitPayConfiguration(
            self::FACADE,
            self::ENV,
            $design,
            $this->getDonation(),
            Mode::STANDARD,
            self::TOKEN,
            self::NOTIFICATION_EMAIL
        );

        // when
        $result = $testedClass->getCurrencyIsoCode();

        // then
        self::assertEquals($currency, $result);
    }

    #[Test]
    public function it_should_return_usd_as_default_currency(): void
    {
        // given
        $posData = $this->createStub(PosData::class);
        $posData->method('getFields')->willReturn([]);
        $design = $this->createStub(Design::class);
        $design->method('getPosData')->willReturn($posData);

        $testedClass = new BitPayConfiguration(
            self::FACADE,
            self::ENV,
            $design,
            $this->getDonation(),
            Mode::STANDARD,
            self::TOKEN,
            self::NOTIFICATION_EMAIL
        );

        // when
        $result = $testedClass->getCurrencyIsoCode();

        // then
        self::assertEquals('USD', $result);
    }

    #[Test]
    public function it_should_return_donation(): void
    {
        $donation = $this->getDonation();

        $testedClass = new BitPayConfiguration(
            self::FACADE,
            self::ENV,
            $this->getDesign(),
            $donation,
            Mode::STANDARD,
            self::TOKEN,
            self::NOTIFICATION_EMAIL
        );

        // when
        $result = $testedClass->getDonation();

        // then
        self::assertSame($donation, $result);
    }

    #[Test]
    public function it_should_return_mode(): void
    {
        $testedClass = new BitPayConfiguration(
            self::FACADE,
            self::ENV,
            $this->getDesign(),
            $this->getDonation(),
            Mode::DONATION,
            self::TOKEN,
            self::NOTIFICATION_EMAIL
        );

        // when
        $result = $testedClass->getMode();

        // then
        self::assertSame(Mode::DONATION, $result);
    }

    private function getTestedClass(?Design $design = null): BitPayConfiguration
    {
        if (!$design) {
            $design = $this->getDesign();
        }

        return new BitPayConfiguration(
            self::FACADE,
            self::ENV,
            $design,
            $this->getDonation(),
            Mode::STANDARD,
            self::TOKEN,
            self::NOTIFICATION_EMAIL
        );
    }

    /**
     * @return Design|\PHPUnit\Framework\MockObject\MockObject
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    private function getDesign(): Design|\PHPUnit\Framework\MockObject\MockObject
    {
        return $this->createMock(Design::class);
    }

    /**
     * @return Donation|\PHPUnit\Framework\MockObject\MockObject
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    private function getDonation(): \PHPUnit\Framework\MockObject\MockObject|Donation
    {
        return $this->createMock(Donation::class);
    }
}
