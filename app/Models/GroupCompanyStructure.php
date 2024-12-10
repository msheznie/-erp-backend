<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupCompanyStructure extends Model
{
    public $table = 'group_company_structure';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $primaryKey = 'id';


    public $fillable = [
        'company_system_id',
        'parent_company_system_id',
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
