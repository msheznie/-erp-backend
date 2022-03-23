<?php

namespace App;

namespace App\Models;

use Eloquent as Model;

class SupplierGroup extends Model
{
    public $table = 'supplier_groups';
        
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $primaryKey  = 'id';

    public $fillable = [
        'group',
        'is_active',
        'is_deleted',
        'deleted_by'
    ];

    protected $casts = [
        'group' => 'string',
        'is_active' => 'boolean',
        'is_deleted' => 'boolean',
        'deleted_by' => 'integer'
    ];

    public static function withDeleted() {
        return SupplierGroup::where('is_active',true)->get();
    }

    public static function onlyNotDeletedAndActive() {
        return SupplierGroup::where('is_active',true)->where('is_deleted',false)->get();
    }

    public static function notDeleted() {
        return SupplierGroup::where('is_deleted',false)->get();
    }
}
