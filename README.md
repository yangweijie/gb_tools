<h1 align="center"> gbTools </h1>

<p align="center"> 国标工具用于验证或生成等</p>


## Installing

```shell
$ composer require yangweijie/gb_tools -vvv
```

## Usage

### 身份证

~~~

$a = new Yangweijie\GbTools\Gb('11643-1999');

$b = $a->isValid('440308199901101512'); //是否有效
var_dump($b);

var_dump($a->getInfo('440308199901101512')); // 获取信息

$c = $a->upgradeId('610104620927690');  // 15位升级
var_dump($c);


$d = $a->fakeId(); //伪造
var_dump($d);
~~~


### 法人和其他组织统一社会信用代码

~~~

$a = new Yangweijie\GbTools\Gb('32100-2015');

$b = $a->isValid('91120222MA06DG3067');
var_dump($b);
~~~

## Contributing

You can contribute in one of three ways:

1. File bug reports using the [issue tracker](https://github.com/yangweijie/gbTools/issues).
2. Answer questions or fix bugs on the [issue tracker](https://github.com/yangweijie/gbTools/issues).
3. Contribute new features or update the wiki.

_The code contribution process is not very formal. You just need to make sure that you follow the PSR-0, PSR-1, and PSR-2 coding guidelines. Any new code contributions must be accompanied by unit tests where applicable._


## 算法

https://github.com/bluesky335/IDCheck

https://github.com/jxlwqq/id-validator

## License

MIT