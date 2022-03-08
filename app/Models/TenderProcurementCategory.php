<?php

namespace App\Models;

use App\Traits\ActiveTrait;
use Illuminate\Database\Eloquent\Model;

class TenderProcurementCategory extends Model
{
    // use ActiveTrait;

    public $table = 'srm_tender_procument_category';

    protected $primaryKey = 'id';

    public $fillable = [
        'description',
        'code',
        'parent_id',
        'level',
        'is_active',
        'created_pc',
        'created_by',
        'created_at',
        'updated_pc',
        'updated_by',
        'updated_at',
        'deleted_by'
    ];

    protected $casts = [
        'description' => 'string',
        'code' => 'string',
        'parent_id' => 'integer',
        'level' => 'string',
        'is_active' => 'integer',
        'isActive' => 'integer',
        'created_pc' => 'string',
        'created_by' => 'string',
        'updated_pc' => 'string',
        'updated_by' => 'string',
        'deleted_by' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

}
