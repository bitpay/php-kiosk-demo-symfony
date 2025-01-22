<?php

/**
 * Copyright (c) 2019 BitPay
 **/

declare(strict_types=1);

namespace App\Twig;

use App\Configuration\BitPayConfiguration;
use App\Configuration\SseConfiguration;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BitPayTwigExtension extends AbstractExtension
{
    private BitPayConfiguration $bitPayConfiguration;
    private SseConfiguration $sseConfiguration;

    public function __construct(BitPayConfiguration $bitPayConfiguration, SseConfiguration $sseConfiguration)
    {
        $this->bitPayConfiguration = $bitPayConfiguration;
        $this->sseConfiguration = $sseConfiguration;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getBitPayConfiguration', [$this, 'getBitPayConfiguration']),
            new TwigFunction('getSseConfiguration', [$this, 'getSseConfiguration'])
        ];
    }

    public function getBitPayConfiguration(): BitPayConfiguration
    {
        return $this->bitPayConfiguration;
    }

    public function getSseConfiguration(): SseConfiguration
    {
        return $this->sseConfiguration;
    }
}
