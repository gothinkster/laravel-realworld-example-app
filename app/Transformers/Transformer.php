<?php

namespace App\Transformers;

abstract class Transformer
{
    public function collection(array $data)
    {
        return array_map([$this, 'item'], $data);
    }

    public abstract function item($data);
}