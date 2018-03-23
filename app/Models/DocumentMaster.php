<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class DocumentMaster
 * @package App\Models
 * @version March 6, 2018, 5:34 am UTC
 *
 * @property string documentID
 * @property string documentDescription
 * @property string departmentID
 * @property string|\Carbon\Carbon timeStamp
 */
class DocumentMaster extends Model
{
    //use SoftDeletes;

    public $table = 'erp_documentmaster';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $primarykey = 'documentSystemID';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'documentID',
        'documentDescription',
        'departmentID',
        'timeStamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'documentDescription' => 'string',
        'departmentID' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
