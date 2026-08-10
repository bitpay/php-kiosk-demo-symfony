<?php

/**
 * Copyright (c) 2019 BitPay
 **/

declare(strict_types=1);

namespace App\Tests\Unit\Configuration;

use App\Configuration\Field;
use App\Configuration\PosData;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PosDataTest extends TestCase
{
    #[Test]
    public function it_should_return_fields(): void
    {
        $field = $this->createMock(Field::class);
        $fields = [$field];

        $posData = new PosData();
        $posData->setFields($fields);

        self::assertEquals($fields, $posData->getFields());
    }
}
