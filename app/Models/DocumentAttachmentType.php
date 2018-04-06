<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class DocumentAttachmentType
 * @package App\Models
 * @version April 3, 2018, 12:19 pm UTC
 *
 * @property string documentID
 * @property string description
 * @property string timestamp
 */
class DocumentAttachmentType extends Model
{
    //use SoftDeletes;

    public $table = 'erp_documentattachmenttype';

    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';
    protected $primaryKey  = 'travelClaimAttachmentTypeID';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'documentID',
        'description',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'travelClaimAttachmentTypeID' => 'integer',
        'documentID' => 'string',
        'description' => 'string',
        'timestamp' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
