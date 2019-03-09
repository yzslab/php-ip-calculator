<?php
/**
 * Created by PhpStorm.
 * Date: 19-3-9
 * Time: 下午9:37
 */

namespace YunInternet\PHPIPCalculator;


use YunInternet\PHPIPCalculator\Calculator\IPv4;
use YunInternet\PHPIPCalculator\Calculator\IPv6;

interface Constants
{
    const UNSIGNED_INT32_MAX = (1 << 32) - 1;

    const TYPE_IPV4 = 0;
    const TYPE_IPV6 = 1;

    const TYPE_2_CALCULATOR = [
        self::TYPE_IPV4 => IPv4::class,
        self::TYPE_IPV6 => IPv6::class,
    ];
}