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
     * @return mixed Calculable format address
     */
    public function getFirstAddress();

    /**
     * @return mixed Calculable format address
     */
    public function getLastAddress();

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
     * @param int|array $position PHP's int is signed int 64, use 4*uint32 array if you need larger number
     * @param null|int $mask If it is null, the default value will be used, IPv4 is 32, IPv6 is 128
     * @return mixed The calculable format IP
     */
    public function ipAt($position, $mask = null);

    /**
     * Similar to ipAt(), but start from the last to the first
     * @param int|array $position
     * @param null|int $mask
     * @return mixed
     */
    public function ipReverseAt($position, $mask = null);

    /**
     * Convert calculable format to human readable string
     * @param mixed $calculableFormat
     * @return string Human readable format IP address
     */
    public static function calculable2HumanReadable($calculableFormat);
}