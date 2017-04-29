<?php

namespace App\Filters;

use ReflectionClass;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

abstract class Filter
{
    protected $request;

    protected $builder;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    protected function getFilterMethods()
    {
        $class  = new ReflectionClass(static::class);

        $methods = array_map(function($method) use ($class) {
            if ($method->class === $class->getName()) {
                return $method->name;
            }
            return null;
        }, $class->getMethods());

        return array_filter($methods);
    }

    protected function getFilters()
    {
        return $this->request->intersect($this->getFilterMethods());
    }

    public function apply(Builder $builder)
    {
        $this->builder = $builder;

        foreach ($this->getFilters() as $name => $value) {
            if (method_exists($this, $name)) {
                $this->$name($value);
            }
        }

        return $this->builder;
    }
}