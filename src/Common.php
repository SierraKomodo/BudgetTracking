<?php

declare(strict_types=1);

namespace SierraKomodo\BudgetTracking;

class Common
{
    /**
     * Converts a number to an accounting-formatted and colored string.
     *
     * @param ?float $amount
     *
     * @return string
     */
    public static function numberToAccounting(float|null $amount): string
    {
        if (is_null($amount)) {
            return '';
        }
        $negative = false;
        if ($amount < 0) {
            $negative = true;
        }
        $amount = abs($amount);
        $amount = number_format($amount, 2);
        $color = 'black';
        if ($amount == 0) {
            $string = '- ';
            $color = 'gray';
        } elseif ($negative) {
            $string = "({$amount})";
            $color = 'red';
        } else {
            $string = "{$amount} ";
        }
        return "<span style='text-align: right; display: inline-block; color: {$color};'>$ {$string}</span>";
    }


    /**
     * Converts a number to a percent-formatted string.
     *
     * @param float $amount
     *
     * @return string
     */
    public static function numberToPercent(float $amount): string
    {
        $amount = number_format($amount, 2);
        return "{$amount}%";
    }
}
