<?php
declare(strict_types=1);

namespace B2Binpay;

use B2Binpay\Exception\NotEqualCurrencyException;
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
        return (string)$this->value;
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
     * @return Decimal
     */
    public function getDecimal(): Decimal
    {
        return $this->value;
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
     * @param Coin $other
     * @return bool
     */
    public function equals(Coin $other): bool
    {
        if ($this->iso !== $other->getIso()) {
            throw new NotEqualCurrencyException();
        }

        return $this->value->comp($other->getDecimal()) === 0;
    }

    /**
     * @param Coin $other
     * @return bool
     */
    public function greaterThan(Coin $other): bool
    {
        if ($this->iso !== $other->getIso()) {
            throw new NotEqualCurrencyException();
        }

        return $this->value->comp($other->getDecimal()) === 1;
    }

    /**
     * @param Coin $other
     * @return bool
     */
    public function lessThan(Coin $other): bool
    {
        if ($this->iso !== $other->getIso()) {
            throw new NotEqualCurrencyException();
        }

        return $this->value->comp($other->getDecimal()) === -1;
    }

    /**
     * @param Coin $amount
     * @return Coin
     */
    public function add(Coin $amount): Coin
    {
    }

    /**
     * @param Coin $amount
     * @return Coin
     */
    public function subtract(Coin $amount): Coin
    {
    }

    /**
     * @param Rate $rate
     * @param int $iso
     * @return Coin
     */
    public function convert(Rate $rate, int $iso): Coin
    {
        if ($this->iso === $iso) {
            return new self((string)$this->value, $this->iso);
        }

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
