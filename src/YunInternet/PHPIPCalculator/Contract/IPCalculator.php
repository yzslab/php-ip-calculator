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
}