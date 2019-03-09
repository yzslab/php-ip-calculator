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
$factory = new YunInternet\PHPIPCalculator\CalculatorFactory\CalculatorFactory("2001:470:0:76::2/96");
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

// v6
$calculator->isIPInRange("2001:470:0:76::ff0f:f0ff");
$calculator->isIPInRange("2001:470:0:78::ffff:ffff");
```
