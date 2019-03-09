<?php
/**
 * Created by PhpStorm.
 * Date: 19-3-9
 * Time: 下午9:29
 */

namespace YunInternet\PHPIPCalculator;


use YunInternet\PHPIPCalculator\Contract\IPCalculator;

class CalculatorFactory
{
    private $ipAddress;

    private $mask;

    private $type;

    /**
     * CalculatorFactory constructor.
     * @param string $ipAddress e.g. 127.0.0.1, 127.0.0.0/8, 127.0.0.0/255.0.0.0, ::1, ::1/64
     * @param null|string|int $mask e.g. 255.255.255.0, 32, 64, 128
     */
    public function __construct($ipAddress, $mask = null)
    {
        $this->type = self::detectAddressType($ipAddress);

        if (is_null($mask)) {
            @list($ipAddress, $mask) = explode("/", $ipAddress);

            // Use default mask
            if (empty($mask)) {
                $mask = $this->type === Constants::TYPE_IPV6 ? 128 : 32;
            }

        }

        $this->ipAddress = $ipAddress;
        $this->mask = $mask;
    }

    /**
     * @return IPCalculator
     */
    public function create()
    {
        $className = $this->getClassName();
        return new $className($this->ipAddress, $this->mask);
    }

    private function getClassName()
    {
        return Constants::TYPE_2_CALCULATOR[$this->type];
    }


    private static function detectAddressType($address)
    {
        return strstr($address, ":") === false ? Constants::TYPE_IPV4 : Constants::TYPE_IPV6;
    }
}