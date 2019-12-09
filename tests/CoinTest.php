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
                'amount' => ['0.0000001', 1000, null],
                'expect' => ['10', 8]
            ],
            [
                'amount' => ['0.0002', 1000, null],
                'expect' => ['20000', 8]
            ],
            [
                'amount' => ['0.0000003000', 1000, null],
                'expect' => ['30', 8]
            ],
            [
                'amount' => ['0.0000000004', 1000, null],
                'expect' => ['1', 8]
            ],
            [
                'amount' => ['50', 1000, 8],
                'expect' => ['50', 8]
            ],
            [
                'amount' => ['1', 1000, 42],
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
        list($amountSum, $amountIso, $amountPow) = $amount;
        list($expectPowed, $expectPrecision) = $expect;

        $amount = new Coin($amountSum, $amountIso, $amountPow);

        $this->assertSame($expectPowed, $amount->getPowed());
        $this->assertSame($expectPrecision, $amount->getPrecision());
    }

    public function equalsDataProvider(): array
    {
        return [
            [
                'amount' => ['0.0000001', 1000, null],
                'other' => ['0.0000001', 1000, null],
                'expect' => true
            ],
            [
                'amount' => ['0.0000001', 1000, null],
                'other' => ['0.0000002', 1000, null],
                'expect' => false
            ],
            [
                'amount' => ['0.2', 1000, null],
                'other' => ['0.02', 1000, null],
                'expect' => false
            ],
        ];
    }

    /**
     * @dataProvider equalsDataProvider
     * @param array $amount
     * @param array $other
     * @param bool $expect
     */
    public function testEquals(array $amount, array $other, bool $expect)
    {
        list($amountSum, $amountIso, $amountPow) = $amount;
        list($otherSum, $otherIso, $otherPow) = $other;

        $amount = new Coin($amountSum, $amountIso, $amountPow);
        $other = new Coin($otherSum, $otherIso, $otherPow);

        $result = $amount->equals($other);
        $this->assertEquals($expect, $result);

    }

    /**
     * @expectedException \B2Binpay\Exception\NotEqualCurrencyException
     */
    public function testEqualsThrowsException()
    {
        $amount = new Coin('1', 1000);
        $other = new Coin('2', 1010);

        $amount->equals($other);
    }

    public function greaterThanDataProvider(): array
    {
        return [
            [
                'amount' => ['0.0000002', 1000, null],
                'other' => ['0.0000001', 1000, null],
                'expect' => true
            ],
            [
                'amount' => ['0.0000001', 1000, null],
                'other' => ['0.0000002', 1000, null],
                'expect' => false
            ],
            [
                'amount' => ['1', 1010, null],
                'other' => ['0.2', 1010, null],
                'expect' => true
            ],
        ];
    }

    /**
     * @dataProvider greaterThanDataProvider
     * @param array $amount
     * @param array $other
     * @param bool $expect
     */
    public function testGreaterThan(array $amount, array $other, bool $expect)
    {
        list($amountSum, $amountIso, $amountPow) = $amount;
        list($otherSum, $otherIso, $otherPow) = $other;

        $amount = new Coin($amountSum, $amountIso, $amountPow);
        $other = new Coin($otherSum, $otherIso, $otherPow);

        $result = $amount->greaterThan($other);
        $this->assertEquals($expect, $result);
    }

    /**
     * @expectedException \B2Binpay\Exception\NotEqualCurrencyException
     */
    public function testGreaterThanThrowsException()
    {
        $amount = new Coin('1', 1000);
        $other = new Coin('2', 1010);

        $amount->greaterThan($other);
    }

    public function lessThanDataProvider(): array
    {
        return [
            [
                'amount' => ['0.0000002', 1000, null],
                'other' => ['0.0000001', 1000, null],
                'expect' => false
            ],
            [
                'amount' => ['0.0000001', 1000, null],
                'other' => ['0.0000002', 1000, null],
                'expect' => true
            ],
            [
                'amount' => ['0.2', 1010, null],
                'other' => ['0.2', 1010, null],
                'expect' => false
            ],
        ];
    }

    /**
     * @dataProvider lessThanDataProvider
     * @param array $amount
     * @param array $other
     * @param bool $expect
     */
    public function testLessThan(array $amount, array $other, bool $expect)
    {
        list($amountSum, $amountIso, $amountPow) = $amount;
        list($otherSum, $otherIso, $otherPow) = $other;

        $amount = new Coin($amountSum, $amountIso, $amountPow);
        $other = new Coin($otherSum, $otherIso, $otherPow);

        $result = $amount->lessThan($other);
        $this->assertEquals($expect, $result);
    }

    /**
     * @expectedException \B2Binpay\Exception\NotEqualCurrencyException
     */
    public function testLessThanThrowsException()
    {
        $amount = new Coin('1', 1000);
        $other = new Coin('2', 1010);

        $amount->lessThan($other);
    }

    public function addDataProvider(): array
    {
        return [
            [
                'amount' => ['0.0000002', 1000, null],
                'other' => ['0.0000001', 1000, null],
                'expect' => '0.0000003'
            ]
        ];
    }

    /**
     * @dataProvider addDataProvider
     * @param array $amount
     * @param array $other
     * @param string $expect
     */
    public function testAdd(array $amount, array $other, string $expect)
    {
        list($amountSum, $amountIso, $amountPow) = $amount;
        list($otherSum, $otherIso, $otherPow) = $other;

        $amount = new Coin($amountSum, $amountIso, $amountPow);
        $other = new Coin($otherSum, $otherIso, $otherPow);

        $result = $amount->add($other)->getValue();
        $this->assertEquals($expect, $result);
    }

    /**
     * @expectedException \B2Binpay\Exception\NotEqualCurrencyException
     */
    public function testAddThrowsException()
    {
        $amount = new Coin('1', 1000);
        $other = new Coin('2', 1010);

        $amount->add($other);
    }

    public function subtractDataProvider(): array
    {
        return [
            [
                'amount' => ['0.0000003', 1000, null],
                'other' => ['0.0000002', 1000, null],
                'expect' => '0.0000001'
            ]
        ];
    }

    /**
     * @dataProvider subtractDataProvider
     * @param array $amount
     * @param array $other
     * @param string $expect
     */
    public function testSubtract(array $amount, array $other, string $expect)
    {
        list($amountSum, $amountIso, $amountPow) = $amount;
        list($otherSum, $otherIso, $otherPow) = $other;

        $amount = new Coin($amountSum, $amountIso, $amountPow);
        $other = new Coin($otherSum, $otherIso, $otherPow);

        $result = $amount->subtract($other)->getValue();
        $this->assertEquals($expect, $result);
    }

    /**
     * @expectedException \B2Binpay\Exception\NotEqualCurrencyException
     */
    public function testSubtractThrowsException()
    {
        $amount = new Coin('1', 1000);
        $other = new Coin('2', 1010);

        $amount->subtract($other);
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