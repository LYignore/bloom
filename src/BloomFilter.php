<?php
namespace Lyignore\Bloom;

use Lyignore\Bloom\Contracts\BloomFilterInterface;
use Lyignore\Bloom\Exceptions\BitArrayException;
use Lyignore\Bloom\Exceptions\BloomFilterException;
use Lyignore\Bloom\Support\Config;
use Lyignore\Bloom\BitArray;

class BloomFilter implements BloomFilterInterface{
    private $config;

    private $redisConfig = [];

    private $bitArray = [];

    private $bitArrayLen;

    private $dataAmount;

    private $decideClass;

    private $hashFunctionAmount;

    private $instanceRedis;

    public function __construct($config){
        if($config instanceof Config){
            $this->config = $config;
        }else{
            $this->config = new Config($config);
        }
        //设置redis配置信息
        $this->redisConfig = $this->config->get('redis');
        if($this->config->get('path')){
            $this->getBinArr($this->config->get('path'));
        }
        //设置bitArr长度
        $this->setBloomConfig();
        $this->calculateOptimumHashFunctionAmount();
    }
    /*
     * 读取bitArray
     */
    public function getBinArr($path){
        if($this->instanceRedis instanceof BitArray){
            return $this->instanceRedis->getArr($path);
        }else{
            $this->instanceRedis = new BitArray($this->redisConfig, $this->config->get('path'));
            return $this->instanceRedis->getArr($path);
        }
    }

    /*
     * 配置总体量信息和bitArray的长度
     */
    private function setBloomConfig(){
        $this->bitArrayLen = $this->config->get('bitArrayLen', 1000000);
        $this->dataAmount = $this->config->get('dataAmount', 10000000);
    }

    /*
     * 设置散列的随机数seed
     */
    private function getRandSeed($str){
        return crc32($str);
    }

    private function mockHashFunction($str){
        $this->hashValuePool = [];
        $seed = $this->getRandSeed($str);
        mt_srand($seed);
        for ($i = 0; $i < $this->hashFunctionAmount; $i++){
            $this->hashValuePool[] = mt_rand(0, $this->bitArrayLen - 1);
        }
    }

    private function calculateOptimumHashFunctionAmount(){
        $this->hashFunctionAmount = ceil(($this->bitArrayLen/$this->dataAmount) * log(2));
    }

    public function add($str){
        if(!$this->instanceRedis instanceof BitArray){
            return new BloomFilterException('Please first instantiate the bit array');
        }
        $this->mockHashFunction($str);
        foreach ($this->hashValuePool as $value) {
            $this->instanceRedis->add($value);
        }
        return $this;
    }

    public function find($str){
        $existsFlag = true;
        $this->mockHashFunction($str);
        foreach ($this->hashValuePool as $value){
            if ($this->instanceRedis->get($value) == 0) {
                $existsFlag = false;
            }
        }
        return $existsFlag;
    }

    public function delete($path){
        if($path == $this->config->get('path')){
            $this->instanceRedis->delete();
            return $this;
        }else{
            return new BloomFilterException('此bitmap非您所创建，删除失败');
        }
    }
}