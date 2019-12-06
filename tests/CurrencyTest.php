<?php
declare(strict_types=1);

namespace B2Binpay\Tests;

use B2Binpay\Currency;
use PHPUnit\Framework\TestCase;

class CurrencyTest extends TestCase
{
    private $currencyIso;
    private $currencyAlpha;
    private $currencyName;
    private $currencyPrecision;

    public function setUp()
    {
        $this->currencyIso = (int)getenv('CURRENCY_ISO');
        $this->currencyAlpha = getenv('CURRENCY_ALPHA');
        $this->currencyName = getenv('CURRENCY_NAME');
        $this->currencyPrecision = (int)getenv('CURRENCY_PRECISION');
    }

    public function testGetAlpha()
    {
        $this->assertSame($this->currencyAlpha, Currency::getAlpha($this->currencyIso));
    }

    /**
     * @expectedException \B2Binpay\Exception\UnknownValueException
     */
    public function testGetAlphaException()
    {
        Currency::getAlpha(9999);
    }

    public function testGetIso()
    {
        $this->assertSame($this->currencyIso, Currency::getIso($this->currencyAlpha));
    }

    /**
     * @expectedException \B2Binpay\Exception\UnknownValueException
     */
    public function testGetIsoException()
    {
        Currency::getIso('test');
    }

    public function testGetPrecision()
    {
        $this->assertSame($this->currencyPrecision, Currency::getPrecision($this->currencyIso));
    }

    /**
     * @expectedException \B2Binpay\Exception\UnknownValueException
     */
    public function testGetPrecisionException()
    {
        Currency::getPrecision(9999);
    }

    public function testGetName()
    {
        $this->assertSame($this->currencyName, Currency::getName($this->currencyIso));
    }

    /**
     * @expectedException \B2Binpay\Exception\UnknownValueException
     */
    public function testGetNameException()
    {
        Currency::getName(9999);
    }

    public function testgetScale()
    {
        $scale = Currency::getScale('100.1');
        $this->assertSame(1, $scale);

        $scale = Currency::getScale('0.003');
        $this->assertSame(3, $scale);

        $scale = Currency::getScale('100');
        $this->assertSame(0, $scale);
    }
}
