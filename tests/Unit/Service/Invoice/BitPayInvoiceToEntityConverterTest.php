<?php

/**
 * Copyright (c) 2019 BitPay
 **/

declare(strict_types=1);

namespace App\Tests\Unit\Service\Invoice;

use App\Entity\Invoice\Invoice;
use App\Repository\Invoice\InvoiceRepositoryInterface;
use App\Service\Invoice\BitPayInvoicePreparator;
use App\Service\Invoice\BitPayInvoiceToEntityConverter;
use App\Tests\ExampleSdkInvoice;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Serializer;

class BitPayInvoiceToEntityConverterTest extends TestCase
{
    /**
     * @test
     */
    public function it_should_map_bitpay_invoice_to_application_invoice(): void
    {
        $serializer = $this->createMock(Serializer::class);
        $invoiceRepository = $this->createMock(InvoiceRepositoryInterface::class);
        $uuid = 'someUuid';

        $testedClass = $this->getTestedClass($serializer, $invoiceRepository);

        $expectedArrayForDenormalize = [
            'buyer' => [
                'name' => null,
                'address1' => null,
                'address2' => null,
                'city' => null,
                'region' => null,
                'postalCode' => null,
                'country' => null,
                'email' => null,
                'phone' => null,
                'notify' => null,
                'providedEmail' => 'test@email.com',
                'providedInfo' => [
                    'name' => 'someName',
                    'phoneNumber' => '123456',
                    'selectedWallet' => 'bitpay',
                    'emailAddress' => 'john@doe.com',
                    'selectedTransactionCurrency' => 'BTC',
                    'sms' => '23423874',
                    'smsVerified' => true,
                ],
            ],
            'payment' => [
                'currencies' => [
                    [
                        'currency_code' => 'BTC',
                        'total' => '29800',
                        'subtotal' => '17500',
                    ],
                    [
                        'currency_code' => 'BCH',
                        'total' => '700700',
                        'subtotal' => '700700',
                    ],
                    [
                        'currency_code' => 'ETH',
                        'total' => '2406000000000000',
                        'subtotal' => '2406000000000000',
                    ],
                    [
                        'currency_code' => 'GUSD',
                        'total' => '1000',
                        'subtotal' => '1000',
                    ],
                    [
                        'currency_code' => 'PAX',
                        'total' => '1.0E+19',
                        'subtotal' => '1.0E+19',
                    ],
                    [
                        'currency_code' => 'BUSD',
                        'total' => '1.0E+19',
                        'subtotal' => '1.0E+19',
                    ],
                    [
                        'currency_code' => 'USDC',
                        'total' => '10000000',
                        'subtotal' => '10000000',
                    ],
                    [
                        'currency_code' => 'XRP',
                        'total' => '6668704',
                        'subtotal' => '6668704',
                    ],
                    [
                        'currency_code' => 'DOGE',
                        'total' => '2077327700',
                        'subtotal' => '2077327700',
                    ],
                    [
                        'currency_code' => 'DAI',
                        'total' => '9.99E+18',
                        'subtotal' => '9.99E+18',
                    ],
                    [
                        'currency_code' => 'WBTC',
                        'total' => '1750',
                        'subtotal' => '1750',
                    ]
                ],
                'amountPaid' => 12,
                'displayAmountPaid' => null,
                'underpaidAmount' => null,
                'overpaidAmount' => null,
                'nonPayProPaymentReceived' => null ?? false,
                'universalCodesPaymentString' => 'https://link.bitpay.com/i/KSnNNfoMDsbRzd1U9ypmVH',
                'universalCodesVerificationLink' => 'https://link.bitpay.com/someLink',
                'transactionCurrency' => 'BTC',
            ],
            'refund' => [
                'info' => [
                    'currencyCode' => 'USD',
                    'supportRequest' => 'supportRequest',
                ],
                'addressesJson' => '["Test refund address"]',
                'addressRequestPending' => false,
                'amounts' => [
                    0 => [
                        'currencyCode' => "BTC",
                        'amount' => 12.42
                    ]
                ]
            ],
            'posDataJson' => '{"store":"store-1","register":"2","reg_transaction_no":"test123","price":"23.54"}',
            'price' => 23.54,
            'currencyCode' => 'USD',
            'bitpayId' => '12',
            'status' => 'pending',
            'createdDate' => 1620734545366,
            'expirationTime' => 1620734880748,
            'bitpayOrderId' => '20210511_abcde',
            'facadeType' => 'pos/invoice',
            'bitpayGuid' => 'payment#1234',
            'exceptionStatus' => 'false',
            'bitpayUrl' => 'https://test.bitpay.com/invoice?id=YUVJ8caCU1DLnUoc4nug4iN',
            'redirectUrl' => 'http://test.com',
            'closeUrl' => 'http://test.com',
            'acceptanceWindow' => 11,
            'token' => '8nPJSGgi7omxcbGGZ4KsSgqdi6juypBe9pVpSURDeAwx4VDQx1XfWPy5qqknDKT9KQ',
            'merchantName' => 'Merchant name',
            'itemDescription' => 'Test item desc',
            'billId' => '34',
            'targetConfirmations' => 6,
            'lowFeeDetected' => false,
            'autoRedirect' => true,
            'shopperUser' => 'someUser',
            'jsonPayProRequired' => false,
            'bitpayIdRequired' => true,
            'isCancelled' => true,
            'transactionSpeed' => 'medium',
            'url' => 'https://test.bitpay.com/invoice?id=YUVJ8caCU1DLnUoc4nug4iN',
            'uuid' => $uuid
        ];

        $serializer->expects(self::once())->method('denormalize')
            ->with($expectedArrayForDenormalize, Invoice::class, null, [])
            ->willReturn(new Invoice());

        $testedClass->execute(ExampleSdkInvoice::create(), $uuid);
    }

    private function getTestedClass($serializer, $invoiceRepository): BitPayInvoiceToEntityConverter
    {
        return new BitPayInvoiceToEntityConverter(
            $serializer,
            new BitPayInvoicePreparator(),
            $invoiceRepository
        );
    }
}
