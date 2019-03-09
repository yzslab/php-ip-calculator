<?php
/**
 * Created by PhpStorm.
 * Date: 19-3-9
 * Time: 下午10:09
 */

namespace YunInternet\PHPIPCalculator\Contract;


interface IPCalculator
{
    /**
     * Calculator type
     * @return int
     */
    public function getType() : int;

    /**
     * @return string
     */
    public function getFirstHumanReadableAddress() : string;

    /**
     * @return string
     */
    public function getLastHumanReadableAddress() : string;

    /**
     * @param string $ipAddress
     * @return bool
     */
    public function isIPInRange($ipAddress) : bool;

    /**
     * Return the $position ip(or subnet) in the subnet
     *
     * e.g.
     * For subnet 127.0.0.0/8, ipAt(0) return 127.0.0.0, ipAt(255) return 127.0.0.255, ipAt(1, 24) return 127.0.1.0
     * @param int $position PHP's int is signed int 64, i think you do not need a larger number
     * @param null|int $mask If it is null, the default value will be used, IPv4 is 32, IPv6 is 128
     * @return mixed The calculable format IP
     */
    public function ipAt($position, $mask = null);

    public static function calculableFormat2HumanReadable($calculableFormat);
}