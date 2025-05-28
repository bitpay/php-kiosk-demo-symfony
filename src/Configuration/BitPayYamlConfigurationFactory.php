<?php

/**
 * Copyright (c) 2019 BitPay
 **/

declare(strict_types=1);

namespace App\Configuration;

use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Yaml\Yaml;

class BitPayYamlConfigurationFactory implements BitPayConfigurationFactoryInterface
{
    private const AVAILABLE_CONFIGURATION_FILES = [
        'application.yaml',
        'application-example.yaml'
    ];

    private SerializerInterface $serializer;
    private string $applicationDir;

    public function __construct(SerializerInterface $serializer, string $applicationDir)
    {
        $this->serializer = $serializer;
        $this->applicationDir = $applicationDir;
    }

    public function create(): BitPayConfigurationInterface
    {
        $configurationFiles = self::AVAILABLE_CONFIGURATION_FILES;
        $configFile = getenv('CONFIG_FILE');
        if ($configFile) {
            $configurationFiles = [$configFile];
        }

        $data = null;
        foreach ($configurationFiles as $configurationFile) {
            try {
                $data = Yaml::parse(
                    file_get_contents($this->applicationDir . DIRECTORY_SEPARATOR . $configurationFile)
                );
                break;
            } catch (\Throwable $e) {
                continue;
            }
        }

        if (!$data) {
            throw new \RuntimeException(sprintf(
                'Invalid configuration. Please create %s file',
                implode(' or ', $configurationFiles)
            ));
        }

        return $this->serializer->denormalize(
            $data['bitpay'],
            BitPayConfiguration::class,
            'yaml'
        );
    }
}
