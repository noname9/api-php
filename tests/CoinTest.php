<?php
declare(strict_types=1);

namespace B2Binpay\Tests;

use B2Binpay\Coin;
use PHPUnit\Framework\TestCase;

class CoinTest extends TestCase
{
    public function getValueDataProvider(): array
    {
        return [
            [
                'amount' => ['0.0000001', null, 1000],
                'expect' => '0.00000010'
            ],
            [
                'amount' => ['0.2', null, 1000],
                'expect' => '0.20000000'
            ],
            [
                'amount' => ['0.0000000003', null, 1000],
                'expect' => '0.00000001'
            ],
            [
                'amount' => ['4', 42, 1000],
                'expect' => '0.00000001'
            ],
            [
                'amount' => ['50', 8, 1000],
                'expect' => '0.00000050'
            ],
            [
                'amount' => ['0.1111111111', null, 1000],
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
        list($amountSum, $amountPow, $amountIso) = $amount;

        $amount = new Coin($amountSum, $amountPow, $amountIso);

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

        $amount = new Coin($amountSum, $amountPow, $amountIso);

        $this->assertSame($expectPowed, $amount->getPowed());
        $this->assertSame($expectPrecision, $amount->getPrecision());
    }

    public function convertDataProvider(): array
    {
        return [
            [
                'amount' => ['1', null, 1000],
                'rate' => ['12345678', 8, null],
                'precision' => 6,
                'expect' => '123457'
            ],
            [
                'amount' => ['0.02', null, 1000],
                'rate' => ['9', null, null],
                'precision' => 2,
                'expect' => '18'
            ],
            [
                'amount' => ['3', 2, 1000],
                'rate' => ['9', null, null],
                'precision' => 2,
                'expect' => '27'
            ],
            [
                'amount' => ['4', 2, 1000],
                'rate' => ['11111', 5, null],
                'precision' => 4,
                'expect' => '45'
            ],
            [
                'amount' => ['5', null, 1000],
                'rate' => ['11111', 5, 1000],
                'precision' => 4,
                'expect' => '5556'
            ],
            [
                'amount' => ['2', 4, null],
                'rate' => ['264866406', 8, null],
                'precision' => 6,
                'expect' => '530'
            ],
            [
                'amount' => ['3', null, null],
                'rate' => ['15242', 8, null],
                'precision' => 8,
                'expect' => '45726'
            ],
            [
                'amount' => ['1', null, null],
                'rate' => ['123456789012345678', 18, null],
                'precision' => 18,
                'expect' => '123456789012345678'
            ],
            [
                'amount' => ['0.01', null, null],
                'rate' => ['5100570', 8, null],
                'precision' => 3,
                'expect' => '1'
            ]
        ];
    }

    /**
     * @dataProvider convertDataProvider
     * @param array $amount
     * @param array $rate
     * @param int $precision
     * @param string $expect
     */
    public function testConvert(array $amount, array $rate, int $precision, string $expect)
    {

        list($amountSum, $amountPow, $amountIso) = $amount;
        list($rateSum, $ratePow, $rateIso) = $rate;

        $amount = new Coin($amountSum, $amountPow, $amountIso);
        $rateObj = new Coin($rateSum, $ratePow, $rateIso);

        $result = $amount->convert($rateObj, $precision)->getPowed();
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

        $amount = new Coin($amountSum, $amountPow, $amountIso);

        $result = $amount->percentage($percent)->getPowed();
        $this->assertEquals($expect, $result);
    }
}
