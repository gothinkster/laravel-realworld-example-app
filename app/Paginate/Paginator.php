<?php

namespace App\Paginate;

use Illuminate\Database\Eloquent\Builder;

class Paginator
{
    protected $total;

    protected $data;

    public function __construct(Builder $builder, $limit = 20, $offset = 0)
    {
        $limit = request()->get('limit', $limit);

        $offset = request()->get('offset', $offset);

        $this->total = $builder->count();

        $this->data = $builder->skip($offset)->take($limit)->get();
    }

    public function getTotal()
    {
        return $this->total;
    }

    public function getData()
    {
        return $this->data;
    }
}