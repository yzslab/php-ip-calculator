<?php
/**
 * Created by PhpStorm.
 * Date: 19-3-9
 * Time: 下午9:34
 */

namespace YunInternet\PHPIPCalculator\Calculator;


use YunInternet\PHPIPCalculator\Constants;
use YunInternet\PHPIPCalculator\Contract\IPCalculator;
use YunInternet\PHPIPCalculator\Exception\ErrorCode;
use YunInternet\PHPIPCalculator\Exception\Exception;

class IPv4 implements IPCalculator
{
    private $binaryIP;

    private $binaryNetwork;

    private $binaryBroadcast;

    private $binaryMask;

    /**
     * IPCalculator constructor.
     * @param string|int $ipv4Address Human readable format or uint32 integer
     * @param string|int $mask
     * @throws Exception
     */
    public function __construct($ipv4Address, $mask)
    {
        if (empty($ipv4Address))
            throw new Exception("Empty IP", ErrorCode::INVALID_IP, null, $ipv4Address);

        if (empty($mask)) {
            $this->binaryMask = self::CIDR2Binary(32);
        } else if (is_numeric($mask)) {
            $cidr = $mask;
            if (!($cidr >= 0 && $cidr <= 32))
                throw new Exception("Invalid CIDR " . $cidr, ErrorCode::INVALID_CIDR, null, $ipv4Address);
            $this->binaryMask = self::CIDR2Binary($cidr);
        } else {
            $this->binaryMask = self::IP2Binary($mask);
            if ($this->binaryMask === false)
                throw new Exception("Invalid netmask " . $mask, ErrorCode::INVALID_NETMASK, null, $ipv4Address);
        }

        if (is_integer($ipv4Address))
            $this->binaryIP = $ipv4Address;
        else
            $this->binaryIP = self::IP2Binary($ipv4Address);

        if ($this->binaryIP > Constants::UNSIGNED_INT32_MAX)
            throw new Exception("Invalid IP", ErrorCode::INVALID_IP, null, $ipv4Address);

        $this->binaryNetwork = $this->binaryIP & $this->binaryMask;

        $this->binaryBroadcast = $this->binaryNetwork | self::convertToUnsignedInteger32(~ $this->binaryMask);
    }

    public function isIPInRange($ip) : bool {
        $binaryIP = self::IP2Binary($ip);
        $binaryNetwork = $binaryIP & $this->binaryMask;
        return $this->binaryNetwork === $binaryNetwork;
    }

    public function binaryIP()
    {
        return $this->binaryIP;
    }

    public function network()
    {
        return $this->binaryNetwork;
    }

    public function broadcast()
    {
        return $this->binaryBroadcast;
    }

    public function getType(): int
    {
        return Constants::TYPE_IPV4;
    }

    public function getFirstHumanReadableAddress(): string
    {
        return long2ip($this->network());
    }

    public function getLastHumanReadableAddress(): string
    {
        return long2ip($this->broadcast());
    }

    public function ipAt($position, $mask = null)
    {
        if (is_null($mask))
            $mask = 32;
        $binaryMask = $this->binaryMask | (($position << (32 - $mask)) & Constants::UNSIGNED_INT32_MAX);
        return $this->binaryBroadcast & $binaryMask;
    }

    public static function calculableFormat2HumanReadable($calculableFormat)
    {
        return long2ip($calculableFormat);
    }

    private static function CIDR2Binary($CIDR)
    {
        $num = 32 - (int) $CIDR;
        return self::convertToUnsignedInteger32(Constants::UNSIGNED_INT32_MAX << $num);
    }

    private static function IP2Binary($ip) {
        return ip2long($ip);
    }

    private static function convertToUnsignedInteger32($value)
    {
        return $value & Constants::UNSIGNED_INT32_MAX;
    }
}