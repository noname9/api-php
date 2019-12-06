<?php
declare(strict_types=1);

namespace B2Binpay\Tests;

use B2Binpay\Coin;
use B2Binpay\Rate;
use PHPUnit\Framework\TestCase;

class CoinTest extends TestCase
{
    public function getValueDataProvider(): array
    {
        return [
            [
                'amount' => ['0.0000001', 1000, null],
                'expect' => '0.00000010'
            ],
            [
                'amount' => ['0.2', 1000, null],
                'expect' => '0.20000000'
            ],
            [
                'amount' => ['0.0000000003', 1000, null],
                'expect' => '0.00000001'
            ],
            [
                'amount' => ['4', 1000, 42],
                'expect' => '0.00000001'
            ],
            [
                'amount' => ['50', 1000, 8],
                'expect' => '0.00000050'
            ],
            [
                'amount' => ['0.1111111111', 1000, null],
                'expect' => '0.11111112'
            ]
        ];
    }

    /**
     * @dataProvider getValueDataProvider
     * @param array $amount
     * @param string $expect
     */
    public function testGetValue(array $amount, string $expect)
    {
        list($amountSum, $amountIso, $amountPow) = $amount;

        $amount = new Coin($amountSum, $amountIso, $amountPow);

        $this->assertSame($expect, $amount->getValue());
    }

    public function getPowedDataProvider(): array
    {
        return [
            [
                'amount' => ['0.0000001', null, 1000],
                'expect' => ['10', 8]
            ],
            [
                'amount' => ['0.0002', null, 1000],
                'expect' => ['20000', 8]
            ],
            [
                'amount' => ['0.0000003000', null, 1000],
                'expect' => ['30', 8]
            ],
            [
                'amount' => ['0.0000000004', null, 1000],
                'expect' => ['1', 8]
            ],
            [
                'amount' => ['50', 8, 1000],
                'expect' => ['50', 8]
            ],
            [
                'amount' => ['1', 42, 1000],
                'expect' => ['1', 8]
            ]
        ];
    }

    /**
     * @dataProvider getPowedDataProvider
     * @param array $amount
     * @param array $expect
     */
    public function testGetPowedAndPrecision(array $amount, array $expect)
    {
        list($amountSum, $amountPow, $amountIso) = $amount;
        list($expectPowed, $expectPrecision) = $expect;

        $amount = new Coin($amountSum, $amountIso, $amountPow);

        $this->assertSame($expectPowed, $amount->getPowed());
        $this->assertSame($expectPrecision, $amount->getPrecision());
    }

    public function convertDataProvider(): array
    {
        return [
            [
                'amount' => ['1', 1000, null],
                'rate' => ['12345678', 8],
                'isoTo' => 1010,
                'expect' => '123457'
            ],
            [
                'amount' => ['0.02', 1000, null],
                'rate' => ['9', null],
                'isoTo' => 840,
                'expect' => '18'
            ],
            [
                'amount' => ['3', 1000, 2],
                'rate' => ['9', null],
                'isoTo' => 840,
                'expect' => '27'
            ],
            [
                'amount' => ['4', 1000, 2],
                'rate' => ['11111', 2],
                'isoTo' => 840,
                'expect' => '445'
            ],
            [
                'amount' => ['5', 1000, null],
                'rate' => ['11111', 5],
                'isoTo' => 840,
                'expect' => '56'
            ],
            [
                'amount' => ['2', 1000, 4],
                'rate' => ['264866406', 4],
                'isoTo' => 840,
                'expect' => '530'
            ],
            [
                'amount' => ['3', 1000, null],
                'rate' => ['15242', 3],
                'isoTo' => 840,
                'expect' => '4572'
            ],
            [
                'amount' => ['1', 1000, null],
                'rate' => ['123456789012345678', 18],
                'isoTo' => 1002,
                'expect' => '123456789012345678'
            ],
            [
                'amount' => ['0.01', 1000, null],
                'rate' => ['5100570', 8],
                'isoTo' => 840,
                'expect' => '1'
            ]
        ];
    }

    /**
     * @dataProvider convertDataProvider
     * @param array $amount
     * @param array $rate
     * @param int $isoTo
     * @param string $expect
     */
    public function testConvert(array $amount, array $rate, int $isoTo, string $expect)
    {

        list($amountSum, $amountIso, $amountPow) = $amount;
        list($rateSum, $ratePow) = $rate;

        $amount = new Coin($amountSum, $amountIso, $amountPow);
        $rate = new Rate($rateSum, $ratePow);

        $result = $amount->convert($rate, $isoTo)->getPowed();
        $this->assertSame($expect, $result);
    }

    public function percentageDataProvider(): array
    {
        return [
            [
                'amount' => ['1', null, 1000],
                'percent' => 10,
                'expect' => '110000000'
            ],
            [
                'amount' => ['0.0021', null, 1000],
                'percent' => 20,
                'expect' => '252000'
            ],
            [
                'amount' => ['0.1', null, 1000],
                'percent' => 90,
                'expect' => '19000000'
            ],
            [
                'amount' => ['0.0000001', null, 1000],
                'percent' => 33,
                'expect' => '14'
            ],
            [
                'amount' => ['111', 6, 1000],
                'percent' => 5,
                'expect' => '11655'
            ]
        ];
    }

    /**
     * @dataProvider percentageDataProvider
     * @param array $amount
     * @param int $percent
     * @param string $expect
     */
    public function testPercentage(array $amount, int $percent, string $expect)
    {
        list($amountSum, $amountPow, $amountIso) = $amount;

        $amount = new Coin($amountSum, $amountIso, $amountPow);

        $result = $amount->percentage($percent)->getPowed();
        $this->assertEquals($expect, $result);
    }
}
