<?php
declare(strict_types=1);

namespace B2Binpay;

/**
 * Coin Factory
 *
 * @package B2Binpay
 */
class CoinFactory
{
    /**
     * @var Currency
     */
    private $currency;

    /**
     * CoinFactory constructor.
     *
     * @param Currency|null $currency
     */
    public function __construct(Currency $currency = null)
    {
        $this->currency = $currency ?? new Currency();
    }

    /**
     * @param string $sum
     * @param int|null $iso
     * @param int|null $pow
     * @return Coin
     */
    public function create(string $sum, int $iso = null, int $pow = null)
    {
        return new Coin($this->currency, $sum, $pow, $iso);
    }
}
