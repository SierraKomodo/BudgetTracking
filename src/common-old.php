<?php

declare(strict_types=1);

namespace SierraKomodo\BudgetTracking;


function numberToAccounting(float|null $amount): string
{
    if (is_null($amount)) {
        return "";
    }
    $negative = false;
    if ($amount < 0) {
        $negative = true;
    }
    $amount = abs($amount);
    $amount = number_format($amount, 2);
    $color = "black";
    if ($amount == 0) {
        $string = "- ";
        $color = "gray";
    } elseif ($negative) {
        $string = "({$amount})";
        $color = "red";
    } else {
        $string = "{$amount} ";
    }
    return "<span style='text-align: right; display: inline-block; color: {$color};'>$ {$string}</span>";
}


function numberToPercent(float $amount): string
{
    $amount = number_format($amount, 2);
    return "{$amount}%";
}
