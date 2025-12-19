<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeSetupDocument extends Model
{

    public $fillable = [
        'documentSystemID',
        'sort'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'documentSystemID' => 'integer',
        'sort' => 'integer'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public function master() {
        return $this->hasOne('App\Models\DocumentMaster','documentSystemID','documentSystemID');
    }

    public function types()
    {
        return $this->hasMany('App\Models\ExchangeSetupDocumentType','exchangeSetupDocumentId')->orderBy('sort');
    }
}
