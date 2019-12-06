<?php
declare(strict_types=1);

namespace B2Binpay\Tests;

use B2Binpay\Coin;
use B2Binpay\CoinFactory;
use PHPUnit\Framework\TestCase;

class CoinFactoryTest extends TestCase
{
    public function testCreate()
    {
        $amount = (new CoinFactory())->create('1');
        $this->assertInstanceOf(Coin::class, $amount);
    }
}
