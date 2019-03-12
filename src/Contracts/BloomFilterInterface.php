<?php
namespace Lyignore\Bloom\Contracts;

interface BloomFilterInterface{
    /*
     * 添加标识进二进制数组
     */
    public function add(string $key);

    /*
     * 判断元素是否在添加过的数组中
     */
    public function find(string $key);

    /*
     * 获取二级制数组
     */
    public function getBinArr(string $path);
}