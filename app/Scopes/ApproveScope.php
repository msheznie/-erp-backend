<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ApproveScope implements Scope
{

    public function apply(Builder $builder, Model $model)
    {
        $builder->where('approved', -1);
    }

    public function remove(Builder $builder)
    {
        $query = $builder->getQuery();
        // here you remove the where close to allow developer load
        // without your global scope condition
        foreach ((array)$query->wheres as $key => $where) {
            if ($where['column'] == 'approved') {
                unset($query->wheres[$key]);
                $query->wheres = array_values($query->wheres);
            }
        }
    }
}