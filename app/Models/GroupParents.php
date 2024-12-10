<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupParents extends Model
{
    public $table = 'group_parents';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $primaryKey = 'id';


    public $fillable = [
        'structure_id',
        'company_system_id',
        'parent_company_system_id',
        'company_relation',
        'group_type',
        'holding_percentage',
        'start_date',
        'end_date'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [

    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];
}
