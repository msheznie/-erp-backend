<?php

namespace App;

namespace App\Models;

use Eloquent as Model;

class SupplierCategory extends Model
{
    public $table = 'supplier_categories';
        
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $primaryKey  = 'id';

    public $fillable = [
        'category',
        'is_active',
        'is_deleted',
        'deleted_by',
        'isDelegation'
    ];

    protected $casts = [
        'category' => 'string',
        'is_active' => 'boolean',
        'is_deleted' => 'boolean',
        'deleted_by' => 'integer'
    ];

    public static function withDeleted() {
        return SupplierCategory::where('is_active',true)->get();
    }

    public static function onlyNotDeletedAndActive() {
        return SupplierCategory::where('is_active',true)->where('is_deleted',false)->get();
    }

    public static function notDeleted() {
        return SupplierCategory::where('is_deleted',false)->get();
    }



}
