# PHP IP Calculator
IPv4 & IPv6 support.

More features is being developed.

## Install
```
composer require yuninternet/php-ip-calculator
```
## Usage
```
$factory = new YunInternet\PHPIPCalculator\CalculatorFactory("192.168.111.222/16");
```
or
```
$factory = new YunInternet\PHPIPCalculator\CalculatorFactory("2001:470:0:76::2/48");
```
then
```
$calculator = $factory->create();

$calculator->getType();
$calculator->getFirstHumanReadableAddress();
$calculator->getLastHumanReadableAddress();

// v4
// Is the target IP in this subnet?
$calculator->isIPInRange("192.168.111.111"); // true;
$calculator->isIPInRange("192.169.111.111"); // false;

// If seperate this subnet to smaller subnet, what is the n subnet's first ip?
$calculator::calculable2HumanReadable($calculator->ipAt(0)); // 192.168.0.0
$calculator::calculable2HumanReadable($calculator->ipAt(65535)); // 192.168.255.255
$calculator::calculable2HumanReadable($calculator->ipAt(255, 24)); // 192.168.255.0;
// Or from then last one to the first.
$calculator::calculable2HumanReadable($calculator->ipReverseAt(255, 24)); // 192.168.0.0
$calculator::calculable2HumanReadable($calculator->ipReverseAt(1, 24)); // 192.168.254.0

// Is the n out of range?
$calculator->isPositionOutOfRange(65535); // false
$calculator->isPositionOutOfRange(255, 24); // false
$calculator->isPositionOutOfRange(256, 24); // true

// Which subnet after this subnet?
$calculator->getSubnetAfter()->getFirstHumanReadableAddress(); // 192.169.0.0
$calculator->getSubnetAfter(87)->getFirstHumanReadableAddress(); // 192.255.0.0
// Or before this subnet?
$calculator->getSubnetBefore()->getFirstHumanReadableAddress(); // 192.167.0.0
$calculator->getSubnetBefore(168)->getFirstHumanReadableAddress(); // 192.0.0.0

// Is the IP1 after IP2?
$calculator::compare($calculator::humanReadable2Calculable("127.0.0.1"), $calculator::humanReadable2Calculable("127.0.0.2")); // -1
$calculator::compare($calculator::humanReadable2Calculable("127.0.0.1"), $calculator::humanReadable2Calculable("127.0.0.1")); // 0
$calculator::compare($calculator::humanReadable2Calculable("127.0.0.2"), $calculator::humanReadable2Calculable("127.0.0.1")); // 1

// After how many subnet is the destination subnet
$calculator->distanceTo($calculator->getSubnetAfter(100); // 100

// How many smaller subnet in this subnet?
$calculator->howMany(); // 65536
$calculator->howMany(24); // 256


// v6
$calculator->isIPInRange("2001:470:0:76::ff0f:f0ff"); // true;
$calculator->isIPInRange("2001:460:0:78::ffff:ffff"); // false;

$calculator::calculable2HumanReadable($calculator->ipAt(2)); // 2001:470::2
$calculator::calculable2HumanReadable($calculator->ipAt(65535, 64)); // 2001:470:0:ffff::
$calculator::calculable2HumanReadable($calculator->ipAt([
    0x0,
    0x0000FFFF,
    0xFFFFFFFF,
    0xFFFFFFFF,
])); // 2001:470:0:ffff:ffff:ffff:ffff:ffff

$calculator::calculable2HumanReadable($calculator->ipReverseAt(0)); // 2001:470:0:ffff:ffff:ffff:ffff:ffff
$calculator::calculable2HumanReadable($calculator->ipReverseAt([
    0x0,
    0x0000FFFF,
    0xFFFFFFFF,
    0xFFFFFFFF,
])); // 2001:470::

$calculator->isPositionOutOfRange([
    0x0,
    0x0000FFFF,
    0xFFFFFFFF,
    0xFFFFFFFF,
]); // false
$calculator->isPositionOutOfRange(65535, 64); // false
$calculator->isPositionOutOfRange(65536, 64); // true

$calculator->getSubnetAfter()->getFirstHumanReadableAddress(); // 2001:470:1::
$calculator->getSubnetAfter(0xffff)->getFirstHumanReadableAddress(); // 2001:470:ffff::

$calculator->getSubnetBefore()->getFirstHumanReadableAddress(); // 2001:46f:ffff::
$calculator->getSubnetBefore(0x4700000)->getFirstHumanReadableAddress(); // 2001::

$calculator::compare($calculator::humanReadable2Calculable("::1"), $calculator::humanReadable2Calculable("::2")); // -1
$calculator::compare($calculator::humanReadable2Calculable("::1"), $calculator::humanReadable2Calculable("::1")); // 0
$calculator::compare($calculator::humanReadable2Calculable("::2"), $calculator::humanReadable2Calculable("::1")); // 1

$calculator->distanceTo($calculator->getSubnetAfter(100); // [0, 0, 0, 100]

$calculator->howMany(48); // [0, 0, 0, 1]
$calculator->howMany(64); // [0, 0, 0, 65536]
```

For more details, please look at test located in tests/YunInternet/PHPIPCalculator/Test:
```
phpunit
```