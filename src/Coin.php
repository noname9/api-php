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
     * @var Decimal
     */
    private $value;

    /**
     * @var int
     */
    private $precision;

    /**
     * @var int
     */
    private $iso;

    /**
     * @param string $sum
     * @param int $iso
     * @param int|null $pow
     */
    public function __construct(string $sum, int $iso, int $pow = null)
    {
        $this->iso = $iso;
        $this->precision = Currency::getPrecision($iso);

        $value = Decimal::fromString($sum);

        if ($pow === null) {
            $scale = max(Currency::getScale($sum), Currency::MAX_PRECISION);
            $value = Decimal::fromString($sum, $scale);
        } else {
            $div = Decimal::fromInteger(10)->pow(Decimal::fromInteger($pow));
            $value = $value->div($div);
        }

        $this->value = $value->ceil($this->precision);
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
     * @return int
     */
    public function getIso(): int
    {
        return $this->iso;
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
     * @param Rate $rate
     * @param int $iso
     * @return Coin
     */
    public function convert(Rate $rate, int $iso): Coin
    {
        $mul = $rate->getDecimal();

        $amount = (string)$this->value->mul($mul);

        return new self($amount, $iso);
    }

    /**
     * @param int $percent
     * @return Coin
     */
    public function percentage(int $percent): Coin
    {
        $mul = Decimal::fromInteger($percent)->div(Decimal::fromInteger(100));

        $amount = (string)$this->value->add($this->value->mul($mul));

        return new self($amount, $this->iso);
    }
}
