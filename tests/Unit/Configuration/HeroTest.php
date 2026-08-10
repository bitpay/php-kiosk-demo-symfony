<?php

/**
 * Copyright (c) 2019 BitPay
 **/

declare(strict_types=1);

namespace App\Tests\Unit\Configuration;

use App\Configuration\Hero;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class HeroTest extends TestCase
{
    #[Test]
    public function it_should_return_bg_color(): void
    {
        $bgColor = '#123';
        $hero = new Hero($bgColor, 'someTitle', 'someBody');

        self::assertEquals($bgColor, $hero->getBgColor());
    }

    #[Test]
    public function it_should_return_title(): void
    {
        $title = 'someTitle';
        $hero = new Hero('#123', $title, 'someBody');

        self::assertEquals($title, $hero->getTitle());
    }

    #[Test]
    public function it_should_return_body(): void
    {
        $body = 'someBody';
        $hero = new Hero('#123', 'someTitle', $body);

        self::assertEquals($body, $hero->getBody());
    }
}
