<?php
namespace Lyignore\Bloom;

use Lyignore\Bloom\Contracts\BitArrayInterface;
use Predis\Client;

class BitArray implements BitArrayInterface{
    private $config = [
        'scheme' => 'tcp',
        'redis_host' => '127.0.0.1',
        'redis_password' => null,
        'redis_port' => '6379'
    ];

    private $handle;

    public static $key = 'bloom';

    public function __construct(array $config, $path = null){
        $this->config = $this->config + $config;
        $this->handle = new Client($this->config);
        if(is_null($path)){
            self::$key = self::$key.':key';
        }else{
            self::$key = self::$key. ':' .$path.':key';
        }
    }

    public function checkExists(){
        return $this->handle->exists(self::$key);
    }

    public function getArr(){
        return self::$key;
    }

    public function add(int $key){
        $this->handle->setbit($this->getArr(), $key, 1);
        return $this->handle;
    }

    public function get(int $key){
        return $this->handle->getbit($this->getArr(), $key);
    }

    public function delete(){
        $this->handle->del(self::$key);
        return $this->handle;
    }
}