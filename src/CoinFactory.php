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
     * @param string $sum
     * @param int|null $iso
     * @param int|null $pow
     * @return Coin
     */
    public function create(string $sum, int $iso = null, int $pow = null)
    {
        return new Coin($sum, $pow, $iso);
    }
}
