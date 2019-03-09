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
$factory = new YunInternet\PHPIPCalculator\CalculatorFactory\CalculatorFactory("2001:470:0:76::2/48");
$calculator = $factory->create();
```
then
```
$calculator->getType();
$calculator->getFirstHumanReadableAddress();
$calculator->getLastHumanReadableAddress();

// v4
$calculator->isIPInRange("192.168.111.111") // true;
$calculator->isIPInRange("192.169.111.111") // false;

$calculator::calculableFormat2HumanReadable($calculator->ipAt(0)); // 192.168.0.0
$calculator::calculableFormat2HumanReadable($calculator->ipAt(65535, 24)); // 192.168.255.0;

// v6
$calculator->isIPInRange("2001:470:0:76::ff0f:f0ff") // true;
$calculator->isIPInRange("2001:460:0:78::ffff:ffff") // false;

$calculator::calculableFormat2HumanReadable($calculator->ipAt(2)); // 2001:470::2
$calculator::calculableFormat2HumanReadable($calculator->ipAt(65535, 64)); // 2001:470:0:ffff::
```

For more details, please look at test located in tests/YunInternet/PHPIPCalculator/Test:
```
phpunit
```