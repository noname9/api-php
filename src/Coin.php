<?php
declare(strict_types=1);

namespace B2Binpay;

use Litipk\BigNumbers\Decimal;

/**
 * Carry about precision according to currency using BigNumbers
 *
 * @package B2Binpay
 */
class Coin
{
    /**
     * @var Currency
     */
    private $currency;

    /**
     * @var Decimal
     */
    private $value;

    /**
     * @var int|null
     */
    private $precision;

    /**
     * @param Currency $currency
     * @param string $sum
     * @param int|null $pow
     * @param int|null $iso
     */
    public function __construct(Currency $currency, string $sum, int $pow = null, int $iso = null)
    {
        $this->currency = $currency;

        $value = Decimal::fromString($sum);

        $this->precision = $iso ? $this->currency->getPrecision($iso) : 0;

        if ($pow === null) {
            $scale = max($this->calcScale($sum), $this->currency->getMaxPrecision());
            $value = Decimal::fromString($sum, $scale);
        } else {
            $div = Decimal::fromInteger(10)->pow(Decimal::fromInteger($pow));
            $value = $value->div($div);
        }

        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        $return = $this->value;

        if (0 !== $this->precision) {
            $return = $this->value->ceil($this->precision);
        }

        return (string)$return;
    }

    /**
     * @return int
     */
    public function getPrecision(): int
    {
        return $this->precision;
    }

    /**
     * @return string
     */
    public function getPowed(): string
    {
        $pow = Decimal::fromInteger($this->precision);
        $mul = Decimal::fromInteger(10)->pow($pow);

        return (string)$this->value->mul($mul)->ceil(0);
    }

    /**
     * @param Coin $rate
     * @param int $precision
     * @return Coin
     */
    public function convert(Coin $rate, int $precision): Coin
    {
        $this->precision = $precision;

        $mul = Decimal::fromString($rate->getValue());

        $this->value = $this->value->mul($mul);

        return $this;
    }

    /**
     * @param int $percent
     * @return Coin
     */
    public function percentage(int $percent): Coin
    {
        $mul = Decimal::fromInteger($percent)->div(Decimal::fromInteger(100));

        $this->value = $this->value->add($this->value->mul($mul));

        return $this;
    }

    /**
     * @param string $sum
     * @return int
     */
    public function calcScale(string $sum): int
    {
        $parts = explode('.', $sum);

        return empty($parts[1]) ? 0 : strlen($parts[1]);
    }
}
