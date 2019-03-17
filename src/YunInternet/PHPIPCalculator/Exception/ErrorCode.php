<?php
/**
 * Created by PhpStorm.
 * Date: 19-3-9
 * Time: 下午10:00
 */

namespace YunInternet\PHPIPCalculator\Exception;


interface ErrorCode
{
    const INVALID_CIDR = 2;
    const INVALID_NETMASK = 3;
    const INVALID_IP = 5;
    const OUT_OF_RANGE = 6;

    const INVALID_MAC_ADDRESS = 8;
}