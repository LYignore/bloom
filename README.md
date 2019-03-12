<h1 align="center"> bloom </h1>

<p align="center"> Bloem algorithm combined with redis implementation.</p>


## Installing

```shell
$ composer require lyignore/bloom -vvv
```

## Usage

对Bloom过滤器进行实现，引入redis的bitmap,替换int模拟的数组，节省了内存空间
可自定义bitmap存储地址，不手动删除bitmap会永久存储
```angular2html
require __DIR__ .'/vendor/autoload.php';

use Lyignore\Bloom\BloomFilter;
use Lyignore\Bloom\BitArray;
$config = [
    'redis' => [
        'scheme' => 'tcp',
        'redis_host' => '127.0.0.1',
        'redis_password' => null,
        'redis_port' => '6379'
    ],
    'path' => 'test:bitArr',
    'bitArrayLen' => 100000,    //位数组长度
    'dataAmount' => 1000000     //集合总量
];
$list = array(
    'http://test/1',
    'http://test/2',
    'http://test/3',
    'http://test/4',
    'http://test/5',
    'http://test/6',
    'http://test/1',
    'http://test/2',
);
$bloom = new BloomFilter($config);
foreach($list as $k => $v){
    if($bloom->find($v)){
        echo $v."已经存在\n";
    }else{
        $bloom->add($v);
        echo "添加".$v."\n";
    }
}

//删除bitmap
$bloom->delete('test:bitArr');

```

## Contributing

You can contribute in one of three ways:

1. File bug reports using the [issue tracker](https://github.com/lyignore/bloom/issues).
2. Answer questions or fix bugs on the [issue tracker](https://github.com/lyignore/bloom/issues).
3. Contribute new features or update the wiki.

_The code contribution process is not very formal. You just need to make sure that you follow the PSR-0, PSR-1, and PSR-2 coding guidelines. Any new code contributions must be accompanied by unit tests where applicable._

## License

MIT