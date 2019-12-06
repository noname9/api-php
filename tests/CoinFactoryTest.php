<?php
declare(strict_types=1);

namespace B2Binpay\Tests;

use B2Binpay\Coin;
use B2Binpay\CoinFactory;
use B2Binpay\Currency;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class CoinFactoryTest extends TestCase
{
    /**
     * @var Currency | MockObject
     */
    private $currency;

    public function setUp()
    {
        $this->currency = $this->createMock(Currency::class);
    }

    public function tearDown()
    {
        $this->currency = null;
    }

    public function testCreate()
    {
        $amount = (new CoinFactory($this->currency))->create('1');
        $this->assertInstanceOf(Coin::class, $amount);
    }
}
