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

        $this->assertEquals("192.168.0.0", $calculator::calculable2HumanReadable($calculator->ipAt(0)));
        $this->assertEquals("192.168.1.0", $calculator::calculable2HumanReadable($calculator->ipAt(256)));
        $this->assertEquals("192.168.0.0", $calculator::calculable2HumanReadable($calculator->ipAt(0, 24)));
        $this->assertEquals("192.168.1.0", $calculator::calculable2HumanReadable($calculator->ipAt(1, 24)));
        $this->assertEquals("192.168.255.0", $calculator::calculable2HumanReadable($calculator->ipAt(255, 24)));

        $this->assertEquals($calculator::calculable2HumanReadable($calculator->ipAt(0)), $calculator::calculable2HumanReadable($calculator->ipReverseAt(65535)));
        $this->assertEquals($calculator::calculable2HumanReadable($calculator->ipAt(0, 24)), $calculator::calculable2HumanReadable($calculator->ipReverseAt(255, 24)));

        $this->assertEquals("192.168.254.0", $calculator::calculable2HumanReadable($calculator->ipReverseAt(1, 24)));

        $this->assertFalse($calculator->isPositionOutOfRange(0));
        $this->assertFalse($calculator->isPositionOutOfRange(65535));
        $this->assertTrue($calculator->isPositionOutOfRange(65536));

        $this->assertFalse($calculator->isPositionOutOfRange(0, 24));
        $this->assertFalse($calculator->isPositionOutOfRange(255, 24));
        $this->assertTrue($calculator->isPositionOutOfRange(256, 24));

    }

    public function testIPv6Calculator()
    {
        $factory = new CalculatorFactory("2001:470:0:76::2/48");
        $calculator = $factory->create();
        $this->assertTrue($calculator instanceof IPv6);

        $this->assertEquals(Constants::TYPE_IPV6, $calculator->getType());
        $this->assertEquals("2001:470::", $calculator->getFirstHumanReadableAddress());
        $this->assertEquals("2001:470:0:ffff:ffff:ffff:ffff:ffff", $calculator->getLastHumanReadableAddress());
        $this->assertTrue($calculator->isIPInRange("2001:470:0:76::ff0f:f0ff"));
        $this->assertFalse($calculator->isIPInRange("2001:460:0:78::ffff:ffff"));

        $this->assertEquals("2001:470::", $calculator::calculable2HumanReadable($calculator->ipAt(0)));
        $this->assertEquals("2001:470::2", $calculator::calculable2HumanReadable($calculator->ipAt(2)));
        $this->assertEquals("2001:470::", $calculator::calculable2HumanReadable($calculator->ipAt(0, 64)));
        $this->assertEquals("2001:470:0:1::", $calculator::calculable2HumanReadable($calculator->ipAt(1, 64)));
        $this->assertEquals("2001:470:0:ffff::", $calculator::calculable2HumanReadable($calculator->ipAt(65535, 64)));

        $this->assertEquals($calculator::calculable2HumanReadable($calculator->ipAt(0, 64)), $calculator::calculable2HumanReadable($calculator->ipReverseAt(65535, 64)));
        $this->assertEquals($calculator::calculable2HumanReadable($calculator->ipReverseAt(65534, 64)), $calculator::calculable2HumanReadable($calculator->ipAt(1, 64)));

        $this->assertEquals("2001:470:0:ffff:ffff:ffff:ffff:ffff", $calculator::calculable2HumanReadable($calculator->ipReverseAt(0)));

        $this->assertEquals("2001:470::", $calculator::calculable2HumanReadable($calculator->ipReverseAt([
            0x0,
            0x0000FFFF,
            0xFFFFFFFF,
            0xFFFFFFFF,
        ])));
        $this->assertEquals("2001:470:0:ffff:ffff:ffff:ffff:ffff", $calculator::calculable2HumanReadable($calculator->ipAt([
            0x0,
            0x0000FFFF,
            0xFFFFFFFF,
            0xFFFFFFFF,
        ])));

        $this->assertFalse($calculator->isPositionOutOfRange(0));
        $this->assertFalse($calculator->isPositionOutOfRange([
            0x0,
            0x0000FFFF,
            0xFFFFFFFF,
            0xFFFFFFFF,
        ]));
        $this->assertTrue($calculator->isPositionOutOfRange([
            0x0,
            0x00010000,
            0x0,
            0x0,
        ]));

        $this->assertFalse($calculator->isPositionOutOfRange(0, 64));
        $this->assertFalse($calculator->isPositionOutOfRange(65535, 64));
        $this->assertTrue($calculator->isPositionOutOfRange(65536, 64));
    }
}