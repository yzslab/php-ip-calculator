<?php
/**
 * Created by PhpStorm.
 * Date: 19-3-9
 * Time: 下午9:33
 */

namespace YunInternet\PHPIPCalculator\Test\Unit;


use PHPUnit\Framework\TestCase;
use YunInternet\PHPIPCalculator\Calculator\IPv4;
use YunInternet\PHPIPCalculator\Calculator\IPv6;
use YunInternet\PHPIPCalculator\CalculatorFactory;
use YunInternet\PHPIPCalculator\Constants;

class CalculatorTest extends TestCase
{
    public function testIPv4Calculator()
    {
        $factory = new CalculatorFactory("192.168.111.222/16");
        $calculator = $factory->create();
        $this->assertTrue($calculator instanceof IPv4);

        $this->assertEquals(Constants::TYPE_IPV4, $calculator->getType());
        $this->assertEquals("192.168.0.0", $calculator->getFirstHumanReadableAddress());
        $this->assertEquals("192.168.255.255", $calculator->getLastHumanReadableAddress());
        $this->assertTrue($calculator->isIPInRange("192.168.111.111"));
        $this->assertFalse($calculator->isIPInRange("192.169.111.111"));
    }

    public function testIPv6Calculator()
    {
        $factory = new CalculatorFactory("2001:470:0:76::2/96");
        $calculator = $factory->create();
        $this->assertTrue($calculator instanceof IPv6);

        $this->assertEquals(Constants::TYPE_IPV6, $calculator->getType());
        $this->assertEquals("2001:470:0:76::", $calculator->getFirstHumanReadableAddress());
        $this->assertEquals("2001:470:0:76::ffff:ffff", $calculator->getLastHumanReadableAddress());
        $this->assertTrue($calculator->isIPInRange("2001:470:0:76::ff0f:f0ff"));
        $this->assertFalse($calculator->isIPInRange("2001:470:0:78::ffff:ffff"));
    }
}