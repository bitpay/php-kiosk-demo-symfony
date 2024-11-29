<?php

/**
 * Copyright (c) 2019 BitPay
 **/

declare(strict_types=1);

namespace App\Service\Serializer;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class BitPayDateTimeDenormalizer implements DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): mixed
    {
        $convertedTimestamp = (int)($data / 1000);
        $dateTime = new \DateTime();
        $dateTime->setTimestamp($convertedTimestamp);

        return \DateTimeImmutable::createFromMutable($dateTime);
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return $context['bitpayTime'] ?? false;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            \DateTimeInterface::class => true,
            \DateTimeImmutable::class => true,
            \DateTime::class => true,
        ];
    }
}
