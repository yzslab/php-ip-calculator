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
    private $networkBits;

    private $decimalValue;

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
     * @param string|int[] $ipv6Address Human readable format or decimal
     * @param string|int|int[] $networkBits
     * @throws Exception
     */
    public function __construct($ipv6Address, $networkBits)
    {
        if (is_array($ipv6Address)) {
            $this->decimalValue = $ipv6Address;
        } else {
            /**
             * @var string $binaryValue 128bit binary IPv6, but it is string
             */
            // Convert IPv6 human readable format to 128bit binary value, which as string in PHP
            $binaryValue = inet_pton($ipv6Address);

            if ($binaryValue === false)
                throw new Exception("Invalid IP", ErrorCode::INVALID_IP, null, $ipv6Address);

            $this->decimalValue = self::ipv6Binary2Decimals($binaryValue);
        }

        if (is_numeric($networkBits)) {
            $networkBits = intval($networkBits);
            if (!($networkBits >= 0 && $networkBits <= 128))
                throw new Exception("Invalid CIDR", ErrorCode::INVALID_CIDR, null);

            $this->decimalMask = self::buildDecimalMask($networkBits);
            $this->networkBits = $networkBits;
        } else {
            throw new Exception("Invalid CIDR", ErrorCode::INVALID_CIDR, null);
        }

        $this->decimalMaskInverted = self::buildInvertedMask($this->decimalMask);
    }

    /**
     * @inheritdoc
     */
    public function getType(): int
    {
        return Constants::TYPE_IPV6;
    }

    /**
     * @inheritdoc
     */
    public function getSubnetAfter($n = 1): IPCalculator
    {
        self::separateInt64($n, $high32bit, $low32Bit);

        if (is_numeric($n))
            $n = [
                0,
                0,
                $high32bit,
                $low32Bit,
            ];

        $nShifted = self::calculableFormatBitLeftShift($n, 128 - $this->networkBits);
        $ip = self::calculableFormatAddition($this->getFirstDecimalIP(), $nShifted);
        return new self($ip, $this->networkBits);
    }

    /**
     * @inheritdoc
     */
    public function getSubnetBefore($n = 1): IPCalculator
    {
        self::separateInt64($n, $high32bit, $low32Bit);

        if (is_numeric($n))
            $n = [
                0,
                0,
                $high32bit,
                $low32Bit,
            ];

        $nShifted = self::calculableFormatBitLeftShift($n, 128 - $this->networkBits);
        $ip = self::calculableFormatSubtract($this->getFirstDecimalIP(), $nShifted);
        return new self($ip, $this->networkBits);
    }


    /**
     * @inheritdoc
     */
    public function getFirstAddress()
    {
        return $this->getFirstDecimalIP();
    }

    /**
     * @inheritdoc
     */
    public function getLastAddress()
    {
        return $this->getLastDecimalIP();
    }

    /**
     * @inheritdoc
     */
    public function getFirstHumanReadableAddress(): string
    {
        if (is_null($this->firstHumanReadableIPv6)) {
            $this->firstHumanReadableIPv6 = inet_ntop($this->getFirstBinaryIP());
        }
        return $this->firstHumanReadableIPv6;
    }

    /**
     * @inheritdoc
     */
    public function getLastHumanReadableAddress(): string
    {
        if (is_null($this->lastHumanReadableIPv6)) {
            $this->lastHumanReadableIPv6 = inet_ntop($this->getLastBinaryIP());
        }
        return $this->lastHumanReadableIPv6;
    }

    /**
     * @inheritdoc
     */
    public function isIPInRange($ipAddress): bool
    {
        $first = $this->getFirstDecimalIP();

        $binaryAsString = inet_pton($ipAddress);
        if ($binaryAsString === false)
            return false;
        // Prevent IPv4
        if (strlen($binaryAsString) !== 16)
            return false;
        $targetIPDecimal = self::ipv6Binary2Decimals($binaryAsString);
        $targetIPAndWithMaskResult = self::calculableFotmarBitAnd($targetIPDecimal, $this->decimalMask);
        for ($i = 0; $i < 4; ++$i) {
            if ($first[$i] !== $targetIPAndWithMaskResult[$i])
                return false;
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function ipAt($position, $mask = null)
    {
        $mask = self::defaultMaskOnNull($mask);
        $bit2Shift = 128 - $mask;

        $originMask = $this->decimalMask;

        if (is_array($position)) {
            $addend = $position;
        } else {
            self::separateInt64($position, $high32Bit, $low32Bit);
            $addend = [
                0,
                0,
                $high32Bit,
                $low32Bit,
            ];
        }

        $decimalMaskBeforeShift = self::calculableFormatBitLeftShift($addend, $bit2Shift);
        $decimalMask = self::calculableFormatBitOr($originMask, $decimalMaskBeforeShift);

        return self::calculableFotmarBitAnd($this->getLastDecimalIP(), $decimalMask);
    }

    public function ipReverseAt($position, $mask = null)
    {
        $mask = self::defaultMaskOnNull($mask);
        $bit2Shift = 128 - $mask;


        $fullOpenMask = [
            Constants::UNSIGNED_INT32_MAX,
            Constants::UNSIGNED_INT32_MAX,
            Constants::UNSIGNED_INT32_MAX,
            Constants::UNSIGNED_INT32_MAX,
        ];

        if (is_array($position)) {
            $subtrahend = $position;
        } else {
            self::separateInt64($position, $high32Bit, $low32Bit);
            $subtrahend = [
                0,
                0,
                $high32Bit,
                $low32Bit,
            ];
        }

        $decimalMaskBeforeShift = self::calculableFormatBitXor($fullOpenMask, $subtrahend);
        $decimalMask = self::calculableFormatBitLeftShift($decimalMaskBeforeShift, $bit2Shift);

        return self::calculableFotmarBitAnd($this->getLastDecimalIP(), $decimalMask);
    }

    /**
     * @inheritdoc
     */
    public function isPositionOutOfRange($position, $mask = null) : bool
    {
        $mask = self::defaultMaskOnNull($mask);
        $bit2Shift = 128 - $mask;

        $originMask = $this->decimalMask;

        if (is_array($position)) {
            $addend = $position;
        } else {
            self::separateInt64($position, $high32Bit, $low32Bit);
            $addend = [
                0,
                0,
                $high32Bit,
                $low32Bit,
            ];
        }

        $decimalMaskBeforeShift = self::calculableFormatBitLeftShift($addend, $bit2Shift);
        $bitAndResult = self::calculableFotmarBitAnd($originMask, $decimalMaskBeforeShift);

        foreach ($bitAndResult as $result) {
            if ($result)
                return true;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public static function compare($first, $second): int
    {
        for ($i = 0; $i < 4; ++$i) {
            if ($first > $second)
                return 1;
            else if ($first < $second)
                return -1;
        }
        return 0;
    }

    /**
     * @inheritdoc
     */
    public static function calculable2HumanReadable($calculableFormat)
    {
        return inet_ntop(self::ipv6Decimal2Binary($calculableFormat));
    }

    public static function humanReadable2Calculable($humanReadable)
    {
        return self::ipv6Binary2Decimals(inet_pton($humanReadable));
    }

    public function getFirstDecimalIP()
    {
        if (is_null($this->firstDecimalIPv6)) {
            $firstIPv6Decimals = self::calculableFotmarBitAnd($this->decimalValue, $this->decimalMask);
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

    private static function defaultMaskOnNull($mask = null)
    {
        return is_null($mask) ? 128 : $mask;
    }

    private static function separateInt64($int64, &$high32Bit, &$low32Bit)
    {
        // Separate $int64 to two uint32
        $high32Bit = $int64 >> 32;
        $low32Bit = $int64 & Constants::UNSIGNED_INT32_MAX;
    }

    private static function DWordCount($totalBit, &$DWord, &$mod)
    {
        $DWord = intdiv($totalBit, 32);
        $mod = $totalBit % 32;
    }

    private static function calculableFormatAddition($addend1, $addend2)
    {
        $additionResult = [];
        $carry = 0;
        for ($i = 3; $i >= 0; --$i) {
            $result = $addend1[$i] + $addend2[$i] + $carry;
            if ($result > Constants::UNSIGNED_INT32_MAX)
                $carry = 1;
            else
                $carry = 0;
            $additionResult[$i] = $result & Constants::UNSIGNED_INT32_MAX;
        }

        return $additionResult;
    }

    private static function calculableFormatSubtract($minuend, $subtrahend)
    {
        $subtractResult = [];
        $carry = 0;
        for ($i = 3; $i >= 0; --$i) {
            $result = $minuend[$i] - $subtrahend[$i] - $carry;
            if ($result < 0)
                $carry = 1;
            else
                $carry = 0;
            $subtractResult[$i] = $result & Constants::UNSIGNED_INT32_MAX;
        }

        return $subtractResult;
    }

    private static function calculableFormatBitOr($addend1, $addend2)
    {
        $additionResult = [];
        for ($i = 3; $i >= 0; --$i) {
            $result = $addend1[$i] | $addend2[$i];
            $additionResult[$i] = $result;
        }

        return $additionResult;
    }

    private static function calculableFormatBitXor($minuend, $subtrahend)
    {
        $subtractResult = [];
        for ($i = 3; $i >= 0; --$i) {
            $result = $minuend[$i] ^ $subtrahend[$i];
            $subtractResult[$i] = $result;
        }

        return $subtractResult;
    }

    private static function calculableFormatBitLeftShift($calculable, $bit)
    {
        self::DWordCount($bit, $skip, $realBitNeed2Shift);

        $shiftedResult = [
            0,
            0,
            0,
            0,
        ];

        $storeAt = 3 - $skip;
        $carry = 0;
        for ($i = 3; $storeAt >= 0; --$i, --$storeAt) {
            $result = $calculable[$i] << $realBitNeed2Shift;
            $shiftedResult[$storeAt] = $result & Constants::UNSIGNED_INT32_MAX | $carry;
            $carry = $result >> 32;
        }

        return $shiftedResult;
    }

    /**
     * @param $decimals
     * @param $mask
     * @return array
     */
    private static function calculableFotmarBitAnd($decimals, $mask) : array
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