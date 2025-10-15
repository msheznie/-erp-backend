<?php

namespace App\Models;

use Eloquent as Model;

class PaymentTypeLanguages extends Model
{
    public $table = 'paymentType_languages';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'paymentTypeId',
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
        'paymentTypeId' => 'integer',
        'languageCode' => 'string',
        'description' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'paymentTypeId' => 'required',
        'languageCode' => 'required',
        'description' => 'required'
    ];

     public function paymentType()
    {
        return $this->belongsTo(PaymentType::class, 'paymentTypeId', 'id');
    }
}
