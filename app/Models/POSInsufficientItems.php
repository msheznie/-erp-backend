<?php

namespace App\Models;

use Eloquent as Model;

class POSInsufficientItems extends Model
{
    public $table = 'pos_insufficient_items';
    public $timestamps = false;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'shiftId',
        'invoiceID',
        'itemAutoId',
        'uom',
        'qty',
        'wareHouseId',
        'availableQty',
        'primaryCode'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'shiftId' => 'integer',
        'itemAutoId' => 'integer',
        'uom' => 'integer',
        'qty' => 'double',
        'wareHouseId' => 'integer',
        'availableQty' => 'double',
        'primaryCode' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public function warehouse(){
        return $this->belongsTo('App\Models\WarehouseMaster', 'wareHouseId', 'wareHouseSystemCode');
    }

}
