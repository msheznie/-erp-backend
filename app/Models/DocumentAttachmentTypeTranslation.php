<?php
/**
 * =============================================
 * -- File Name : DocumentAttachmentTypeTranslation.php
 * -- Project Name : ERP
 * -- Module Name :  Document Attachment Type Translation
 * -- Author : System Generated
 * -- Create date : 13- September 2025
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class DocumentAttachmentTypeTranslation
 * @package App\Models
 * @version September 13, 2025, 5:00 pm UTC
 *
 * @property integer travelClaimAttachmentTypeID
 * @property string languageCode
 * @property string description
 */
class DocumentAttachmentTypeTranslation extends Model
{
    //use SoftDeletes;

    public $table = 'erp_documentattachmenttype_translation';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'travelClaimAttachmentTypeID',
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
        'travelClaimAttachmentTypeID' => 'integer',
        'languageCode' => 'string',
        'description' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'travelClaimAttachmentTypeID' => 'required|integer',
        'languageCode' => 'required|string|max:10',
        'description' => 'required|string|max:255'
    ];

    /**
     * Get the document attachment type that owns the translation.
     */
    public function documentAttachmentType()
    {
        return $this->belongsTo(DocumentAttachmentType::class, 'travelClaimAttachmentTypeID', 'travelClaimAttachmentTypeID');
    }
}
