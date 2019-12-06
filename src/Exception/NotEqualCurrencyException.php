<?php

namespace B2Binpay\Exception;

class NotEqualCurrencyException extends B2BinpayException
{
    public function __construct($message = null)
    {
        if ($message === null)
        {
            $message ='You should convert values to the same currency';
        }

        parent::__construct($message);
    }
}
