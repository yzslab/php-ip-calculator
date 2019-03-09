<?php
/**
 * Created by PhpStorm.
 * Date: 19-3-9
 * Time: 下午9:38
 */

namespace YunInternet\PHPIPCalculator\Exception;


class Exception extends \Exception
{
    public function __construct(string $message = "", int $code = 0, \Throwable $previous = null, $ip = "")
    {
        parent::__construct($message, $code, $previous);
    }
}