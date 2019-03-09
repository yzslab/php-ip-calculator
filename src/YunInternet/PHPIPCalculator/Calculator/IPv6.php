<?php
/**
 * Created by PhpStorm.
 * Date: 19-3-9
 * Time: 下午9:31
 */

namespace YunInternet\PHPIPCalculator\Calculator;


use YunInternet\PHPIPCalculator\Constants;
use YunInternet\PHPIPCalculator\Contract\IPCalculator;
use YunInternet\PHPIPCalculator\Exception\ErrorCode;
use YunInternet\PHPIPCalculator\Exception\Exception;

class IPv6 implements IPCalculator
{
    /**
     * @var string 128bit binary IPv6, but it is string
     */
    private $binaryValue;

    private $decimalValue;

    private $mask;

    private $decimalMask;

    private $decimalMaskInverted;

    private $firstDecimalIPv6;

    private $lastDecimalIPv6;

    private $firstBinaryIPv6;

    private $lastBinaryIPv6;

    private $firstHumanReadableIPv6;

    private $lastHumanReadableIPv6;

    /**
     * IPv6 constructor.
     * @param string $ipv6Address
     * @param string|int $mask
     * @throws Exception
     */
    public function __construct($ipv6Address, $mask)
    {
        // Convert IPv6 human readable format to 128bit binary value, which as string in PHP
        $this->binaryValue = inet_pton($ipv6Address);

        if ($this->binaryValue === false)
            throw new Exception("Invalid IP", ErrorCode::INVALID_IP, null, $ipv6Address);

        if (!is_numeric($mask))
            throw new Exception("Invalid CIDR", ErrorCode::INVALID_CIDR, null);
        $this->mask = intval($mask);
        if (!($this->mask >= 0 && $this->mask <= 128))
            throw new Exception("Invalid CIDR", ErrorCode::INVALID_CIDR, null);

        $this->decimalValue = self::ipv6Binary2Decimals($this->binaryValue);
        $this->decimalMask = self::buildDecimalMask($mask);
        $this->decimalMaskInverted = self::buildInvertedMask($this->decimalMask);
    }

    public function getType(): int
    {
        return Constants::TYPE_IPV6;
    }

    public function getFirstHumanReadableAddress(): string
    {
        if (is_null($this->firstHumanReadableIPv6)) {
            $this->firstHumanReadableIPv6 = inet_ntop($this->getFirstBinaryIP());
        }
        return $this->firstHumanReadableIPv6;
    }

    public function getLastHumanReadableAddress(): string
    {
        if (is_null($this->lastHumanReadableIPv6)) {
            $this->lastHumanReadableIPv6 = inet_ntop($this->getLastBinaryIP());
        }
        return $this->lastHumanReadableIPv6;
    }

    public function isIPInRange($ipAddress): bool
    {
        $first = $this->getFirstDecimalIP();
        $targetIPDecimal = self::ipv6Binary2Decimals(inet_pton($ipAddress));
        $targetIPAndWithMaskResult = self::decimalIPBitAndWithMask($targetIPDecimal, $this->decimalMask);
        for ($i = 0; $i < 4; ++$i) {
            if ($first[$i] !== $targetIPAndWithMaskResult[$i])
                return false;
        }
        return true;
    }

    public function getFirstDecimalIP()
    {
        if (is_null($this->firstDecimalIPv6)) {
            $firstIPv6Decimals = self::decimalIPBitAndWithMask($this->decimalValue, $this->decimalMask);
            $this->firstDecimalIPv6 = $firstIPv6Decimals;
        }
        return $this->firstDecimalIPv6;
    }

    public function getLastDecimalIP()
    {
        if (is_null($this->lastDecimalIPv6)) {
            $lastIPv6Decimals = [];
            for ($i = 0; $i < 4; ++$i) {
                $andWithMask = $this->decimalValue[$i] | $this->decimalMaskInverted[$i];
                $lastIPv6Decimals[] = $andWithMask;
            }
            $this->lastDecimalIPv6 = $lastIPv6Decimals;
        }
        return $this->lastDecimalIPv6;
    }

    public function getFirstBinaryIP()
    {
        if (is_null($this->firstBinaryIPv6)) {
            $this->firstBinaryIPv6 = self::ipv6Decimal2Binary($this->getFirstDecimalIP());
        }
        return $this->firstBinaryIPv6;
    }

    public function getLastBinaryIP()
    {
        if (is_null($this->lastBinaryIPv6)) {
            $this->lastBinaryIPv6 = self::ipv6Decimal2Binary($this->getlastDecimalIP());
        }
        return $this->lastBinaryIPv6;
    }

    /**
     * @param $decimals
     * @param $mask
     * @return array
     */
    private static function decimalIPBitAndWithMask($decimals, $mask) : array
    {
        $result = [];
        for ($i = 0; $i < 4; ++$i) {
            $andWithMask = $decimals[$i] & $mask[$i];
            $result[] = $andWithMask;
        }
        return $result;
    }

    /**
     * @param string|int $mask
     * @return array
     */
    private static function buildDecimalMask($mask) : array
    {
        $totalBit2Build = $mask;

        $ipv6Mask = [];
        for ($i = 0; $i < 4; ++$i) {
            $bit2Build = min(32, $totalBit2Build);
            $totalBit2Build = max(0, $totalBit2Build - $bit2Build);
            $mask = (Constants::UNSIGNED_INT32_MAX << (32 - $bit2Build)) & Constants::UNSIGNED_INT32_MAX;
            $ipv6Mask[] = $mask;
        }

        return $ipv6Mask;
    }

    /**
     * @param $decimalMask
     * @return array
     */
    private static function buildInvertedMask(array $decimalMask) : array
    {
        $ipv6MaskInverted = [];
        for ($i = 0; $i < 4; ++$i) {
            $ipv6MaskInverted[] = (~$decimalMask[$i]) & Constants::UNSIGNED_INT32_MAX;
        }

        return $ipv6MaskInverted;
    }

    /**
     * Convert 128bit binary IPv6 address to 128bit binary IPv6 address binary(uhhuh, from type string to long), store in array, index 0 is the high 32bit.
     * Maybe you think this is unnecessary, because $ipv6Binary is already the binary value, but seems like PHP does not provide any function for you to cast string to int directly
     * @param string $ipv6Binary
     * @return array
     */
    private static function ipv6Binary2Decimals($ipv6Binary) : array
    {
        $ipv6Decimals = [];
        for ($i = 0; $i < 4; ++$i) {
            $ipv6SubBit = substr($ipv6Binary, $i * 4, 4);
            $ipv6Decimals[] = hexdec(bin2hex($ipv6SubBit));
        }
        return $ipv6Decimals;
    }

    /**
     * @param $ipv6Decimal
     * @return string
     */
    private static function ipv6Decimal2Binary($ipv6Decimal)
    {
        $binary = "";
        foreach ($ipv6Decimal as $decimal) {
            $hexadecimalString = str_pad(dechex($decimal), 8, "0", STR_PAD_LEFT);
            $binary .= hex2bin($hexadecimalString);
        }
        return $binary;
    }
}