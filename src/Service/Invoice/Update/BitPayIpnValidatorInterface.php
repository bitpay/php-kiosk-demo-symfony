<?php

declare(strict_types=1);

namespace App\Service\Invoice\Update;

interface BitPayIpnValidatorInterface
{
    public function execute(array $data, array $headers): void;
}
