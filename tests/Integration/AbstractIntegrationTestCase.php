<?php

/**
 * Copyright (c) 2019 BitPay
 **/

declare(strict_types=1);

namespace App\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractIntegrationTestCase extends WebTestCase
{
    public function setUp(): void
    {
        putenv("CONFIG_FILE=application-example.yaml");
        parent::setUp();
    }

    public function tearDown(): void
    {
        putenv("CONFIG_FILE=");
        parent::tearDown();
    }
}
