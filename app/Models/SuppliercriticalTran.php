<?php

namespace App\Models;

use Eloquent as Model;

class SuppliercriticalTran extends Model
{
    public $table = 'suppliercritical_translations';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'suppliercriticalID',
        'languageCode',
        'description'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'suppliercriticalID' => 'integer',
        'languageCode' => 'string',
        'description' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'suppliercriticalID' => 'required',
        'languageCode' => 'required',
        'description' => 'required'
    ];

     public function suppliercritical()
    {
        return $this->belongsTo(SupplierCritical::class, 'suppliercriticalID', 'suppliercriticalID');
    }
}
