<?php
namespace Lyignore\Bloom\Contracts;

interface BitArrayInterface{
    public function add(int $key);

    public function get(int $key);

    public function delete();
}