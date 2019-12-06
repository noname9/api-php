<?php
declare(strict_types=1);

namespace B2Binpay;

use Litipk\BigNumbers\Decimal;

class Rate
{
    /**
     * @var Decimal
     */
    private $value;

    /**
     * Rate constructor.
     * @param string $value
     * @param int $pow
     */
    public function __construct(string $value, int $pow = null)
    {
        if ($pow !== null) {
            $div = Decimal::fromInteger(10)->pow(Decimal::fromInteger($pow));
            $this->value = Decimal::fromString($value)->div($div);
        } else {
            $this->value = Decimal::fromString($value);
        }
    }

    /**
     * @return Decimal
     */
    public function getDecimal(): Decimal
    {
        return $this->value;
    }
}
