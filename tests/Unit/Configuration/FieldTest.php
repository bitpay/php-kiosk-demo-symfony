<?php

/**
 * Copyright (c) 2019 BitPay
 **/

declare(strict_types=1);

namespace App\Tests\Unit\Configuration;

use App\Configuration\Field;
use App\Configuration\Option;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class FieldTest extends TestCase
{
    #[Test]
    public function it_should_provide_currency(): void
    {
        $currency = 'USD';
        $field = new Field();
        $field->setCurrency($currency);

        $this->assertEquals($currency, $field->getCurrency());
    }

    #[Test]
    public function it_should_provide_id(): void
    {
        $field = new Field();
        $id = 'someId';
        $field->setId($id);

        $this->assertEquals($id, $field->getId());
    }

    #[Test]
    public function it_should_provide_label(): void
    {
        $field = new Field();
        $label = 'someLabel';
        $field->setLabel($label);

        $this->assertEquals($label, $field->getLabel());
    }

    #[Test]
    public function it_should_provide_name(): void
    {
        $name = 'someName';
        $field = new Field();
        $field->setName($name);

        $this->assertEquals($name, $field->getName());
    }

    #[Test]
    public function it_should_provide_options(): void
    {
        $option = $this->createMock(Option::class);
        $options = [$option];
        $field = new Field();
        $field->setOptions($options);

        $this->assertEquals($options, $field->getOptions());
    }

    #[Test]
    public function it_should_provide_required(): void
    {
        $field = new Field();

        $field->setRequired(true);
        $this->assertEquals(true, $field->isRequired());

        $field->setRequired(false);
        $this->assertEquals(false, $field->isRequired());
    }

    #[Test]
    public function it_should_provide_type(): void
    {
        $type = 'someType';
        $field = new Field();
        $field->setType($type);

        $this->assertEquals($type, $field->getType());
    }
}
