<?php

/**
 * Copyright (c) 2019 BitPay
 **/

declare(strict_types=1);

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AbstractFunctionalTestCase extends WebTestCase
{
    public function setUp(): void
    {
        putenv("CONFIG_FILE=application-functional.yaml");

        parent::setUp();
    }

    public function tearDown(): void
    {
        putenv("CONFIG_FILE=");
        parent::tearDown();
    }
}
