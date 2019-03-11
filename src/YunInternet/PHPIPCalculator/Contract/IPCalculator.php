<?php
/**
 * Created by PhpStorm.
 * Date: 19-3-9
 * Time: 下午10:09
 */

namespace YunInternet\PHPIPCalculator\Contract;


use YunInternet\PHPIPCalculator\Exception\Exception;

interface IPCalculator
{
    /**
     * Calculator type
     * @return int
     */
    public function getType() : int;

    /**
     * Return the subnet after this subnet
     * @param int|int[] $n
     * @return IPCalculator
     * @throws Exception
     */
    public function getSubnetAfter($n = 1) : IPCalculator;

    /**
     * Similar to getSubnetAfter()
     * @param int|int[] $n
     * @return IPCalculator
     * @throws Exception
     */
    public function getSubnetBefore($n = 1) : IPCalculator;

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
     * @param int|int[] $position PHP's int is signed int 64, use 4*uint32 array if you need larger number
     * @param null|int $mask If it is null, the default value will be used, IPv4 is 32, IPv6 is 128
     * @return mixed The calculable format IP
     */
    public function ipAt($position, $mask = null);

    /**
     * Work the same as ipAt(), but return IPCalculator
     * @param int|int[] $position PHP's int is signed int 64, use 4*uint32 array if you need larger number
     * @param null|int $mask If it is null, the default value will be used, IPv4 is 32, IPv6 is 128
     * @return mixed The calculable format IP
     * @throws Exception
     */
    public function ipAtAsCalculator($position, $mask = null) : IPCalculator;

    /**
     * Similar to ipAt(), but start from the last to the first
     * @param int|int[] $position
     * @param null|int $mask
     * @return mixed
     */
    public function ipReverseAt($position, $mask = null);

    /**
     * Work as ipReverseAt(), but return IPCalculator
     * @param int|int[] $position
     * @param null|int $mask
     * @return mixed
     * @throws Exception
     */
    public function ipReverseAtAsCalculator($position, $mask = null) : IPCalculator;

    /**
     * @param int|array $position
     * @param null|int $mask
     * @return bool
     */
    public function isPositionOutOfRange($position, $mask = null) : bool;

    /**
     * Compare two calculable format ip, return -1 on $first < $second, 0 on $first equal to $second, 1 on $first > $second
     * @param mixed $first
     * @param mixed $second
     * @return int
     */
    public static function compare($first, $second) : int;

    /**
     * Convert calculable format to human readable string
     * @param mixed $calculableFormat
     * @return string Human readable format IP address
     */
    public static function calculable2HumanReadable($calculableFormat);

    /**
     * @param $humanReadable
     * @return mixed|false
     */
    public static function humanReadable2Calculable($humanReadable);
}