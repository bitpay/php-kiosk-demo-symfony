<?php

/**
 * Copyright (c) 2019 BitPay
 **/

declare(strict_types=1);

namespace App\Tests\Unit\Configuration;

use App\Configuration\Design;
use App\Configuration\Hero;
use App\Configuration\PosData;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DesignTest extends TestCase
{
    #[Test]
    public function it_should_provide_hero(): void
    {
        $hero = $this->createMock(Hero::class);
        $logo = 'someLogo.png';
        $posData = $this->createMock(PosData::class);

        $testedClass = new Design($hero, $logo, $posData);
        self::assertEquals($hero, $testedClass->getHero());
    }

    #[Test]
    public function it_should_provide_logo(): void
    {
        $hero = $this->createMock(Hero::class);
        $logo = 'someLogo.png';
        $posData = $this->createMock(PosData::class);

        $testedClass = new Design($hero, $logo, $posData);
        self::assertEquals($logo, $testedClass->getLogo());
    }

    #[Test]
    public function it_should_provide_pos_data(): void
    {
        $hero = $this->createMock(Hero::class);
        $logo = 'someLogo.png';
        $posData = $this->createMock(PosData::class);

        $testedClass = new Design($hero, $logo, $posData);
        self::assertEquals($posData, $testedClass->getPosData());
    }
}
