<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TenderProcurementCategory extends Model
{
    public $table = 'srm_tender_procument_category';

    use SoftDeletes;

    protected $primaryKey = 'id';

    public $fillable = [
        'description',
        'description_in_secondary',
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

    public function tenderMaster()
    {
        return $this->hasMany('App\Models\TenderMaster', 'procument_cat_id', 'id');
    }

    public function procumentActivity()
    {
        return $this->hasMany('App\Models\ProcumentActivity', 'category_id', 'id');
    }
    public static function getTenderProcurementCatDrop($procurementCatID){
        return self::where('id', $procurementCatID)->first();
    }
    public static function getAllProcurementCategory(){
        return self::where('level', 0)->where('is_active', 1)->get();
    }

    public static function getTenderProcurementCat($procurementCatId){
        return self::where('parent_id', $procurementCatId)->where('is_active', 1)->get();
    }
}
