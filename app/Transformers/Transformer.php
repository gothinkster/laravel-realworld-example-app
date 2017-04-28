<?php

namespace App\Transformers;

abstract class Transformer
{
    protected $resourceName = 'data';

    public function collection(array $data)
    {
        return [
            str_plural($this->resourceName) => array_map([$this, 'transform'], $data)
        ];
    }

    public function item($data)
    {
        return [
            $this->resourceName => $this->transform($data)
        ];
    }

    public abstract function transform($data);
}