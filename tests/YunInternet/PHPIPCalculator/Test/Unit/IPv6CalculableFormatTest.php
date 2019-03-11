<?php
/**
 * Created by PhpStorm.
 * Date: 19-3-11
 * Time: 下午10:55
 */

namespace YunInternet\PHPIPCalculator\Test\Unit;


use PHPUnit\Framework\TestCase;
use YunInternet\PHPIPCalculator\Calculator\IPv6;

class IPv6CalculableFormatTest extends TestCase
{
    public function testAddition()
    {
        $addition = $this->getMethod("calculableFormatAddition");
        $this->assertEquals([0, 0, 1, 0], $addition->invoke(null, [0, 0, 0, 0xFFFFFFFF], [0, 0, 0, 1]));
        $this->assertEquals([0, 1, 0, 0], $addition->invoke(null, [0, 0, 0xFFFFFFFF, 0xFFFFFFFF], [0, 0, 0, 1]));
        $this->assertEquals([1, 0, 0, 0], $addition->invoke(null, [0, 0xFFFFFFFF, 0xFFFFFFFF, 0xFFFFFFFF], [0, 0, 0, 1]));
        $this->assertEquals([0, 0, 0, 0], $addition->invoke(null, [0xFFFFFFFF, 0xFFFFFFFF, 0xFFFFFFFF, 0xFFFFFFFF], [0, 0, 0, 1]));

        $this->assertEquals([0, 0, 1, 1], $addition->invoke(null, [0, 0, 0, 0xFFFFFFFF], [0, 0, 0, 2]));
        $this->assertEquals([0, 1, 1, 1], $addition->invoke(null, [0, 0, 0xFFFFFFFF, 0xFFFFFFFF], [0, 0, 1, 2]));
        $this->assertEquals([1, 1, 1, 1], $addition->invoke(null, [0, 0xFFFFFFFF, 0xFFFFFFFF, 0xFFFFFFFF], [0, 1, 1, 2]));
        $this->assertEquals([1, 1, 1, 1], $addition->invoke(null, [0xFFFFFFFF, 0xFFFFFFFF, 0xFFFFFFFF, 0xFFFFFFFF], [1, 1, 1, 2]));
    }

    public function testSubtract()
    {

        $subtract = $this->getMethod("calculableFormatSubtract");
        $this->assertEquals([0, 0, 0, 0xFFFFFFFF], $subtract->invoke(null, [0, 0, 1, 0], [0, 0, 0, 1]));
        $this->assertEquals([0, 0, 0xFFFFFFFF, 0xFFFFFFFF], $subtract->invoke(null, [0, 1, 0, 0], [0, 0, 0, 1]));
        $this->assertEquals([0, 0xFFFFFFFF, 0xFFFFFFFF, 0xFFFFFFFF], $subtract->invoke(null, [1, 0, 0, 0], [0, 0, 0, 1]));
        $this->assertEquals([0xFFFFFFFF, 0xFFFFFFFF, 0xFFFFFFFF, 0xFFFFFFFF], $subtract->invoke(null, [0, 0, 0, 0], [0, 0, 0, 1]));

        $this->assertEquals([0xFFFFFFFF, 0xFFFFFFFF, 0xFFFFFFFF, 0xFFFFFFFD], $subtract->invoke(null, [0, 0, 1, 0], [0, 0, 1, 3]));
        $this->assertEquals([0xFFFFFFFF, 0xFFFFFFFF, 0xFFFFFFFE, 0xFFFFFFFD], $subtract->invoke(null, [0, 1, 0, 0], [0, 1, 1, 3]));
        $this->assertEquals([0xFFFFFFFF, 0xFFFFFFFE, 0xFFFFFFFE, 0xFFFFFFFD], $subtract->invoke(null, [1, 0, 0, 0], [1, 1, 1, 3]));
        $this->assertEquals([0xFFFFFFFE, 0xFFFFFFFE, 0xFFFFFFFE, 0xFFFFFFFD], $subtract->invoke(null, [0, 0, 0, 0], [1, 1, 1, 3]));
    }

    /**
     * @param $name
     * @return \ReflectionMethod
     * @throws \ReflectionException
     */
    private function getMethod($name)
    {
        $reflectionClass = new \ReflectionClass(IPv6::class);
        $method = $reflectionClass->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }
}