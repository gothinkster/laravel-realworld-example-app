<?php

namespace App\Transformers;

use Illuminate\Support\Collection;

abstract class Transformer
{
    protected $resourceName = 'data';

    public function collection(Collection $data)
    {
        return [
            str_plural($this->resourceName) => $data->map([$this, 'transform'])
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
