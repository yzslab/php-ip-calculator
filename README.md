# PHP IP Calculator
IPv4 & IPv6 support.

More features is being developed.

## Install
```
composer require yuninternet/php-ip-calculator
```
## Usage
```
$factory = new YunInternet\PHPIPCalculator\CalculatorFactory\CalculatorFactory("192.168.111.222/16");
$calculator = $factory->create();
```
or
```
$factory = new CalculatorFactory("2001:470:0:76::2/96");
$calculator = $factory->create();
```
then
```
$calculator->getType();
$calculator->getFirstHumanReadableAddress();
$calculator->getLastHumanReadableAddress();

// v4
$calculator->isIPInRange("192.168.111.111");
$calculator->isIPInRange("192.169.111.111");

$calculator::calculableFormat2HumanReadable($calculator->ipAt(0)); // 192.168.0.0
$calculator::calculableFormat2HumanReadable($calculator->ipAt(65535, 24)); // 192.168.255.0;

// v6
$calculator->isIPInRange("2001:470:0:76::ff0f:f0ff");
$calculator->isIPInRange("2001:470:0:78::ffff:ffff");

$calculator::calculableFormat2HumanReadable($calculator->ipAt(2)); // 2001:470::2
$calculator::calculableFormat2HumanReadable($calculator->ipAt(65535, 64)); // 2001:470:0:ffff::
```