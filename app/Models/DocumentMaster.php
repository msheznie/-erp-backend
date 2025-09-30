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
    const UPDATED_AT = 'timeStamp';
    protected $primaryKey = 'documentSystemID';

    protected $appends = ['documentDescription'];

    protected $dates = ['deleted_at'];


    public $fillable = [
        'documentID',
        'documentDescription',
        'departmentSystemID',
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
        'departmentSystemID' => 'integer',
        'departmentID' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function approval_levels(){
        return $this->hasMany('App\Models\ApprovalLevel','documentSystemID','documentSystemID');
    }

    function company_document_attachment(){
        return $this->hasOne(CompanyDocumentAttachment::class, 'documentSystemID');
    }

    public static function getDocumentData($documentSystemId){
        return DocumentMaster::select('documentID', 'documentSystemID')
            ->where('documentSystemID', $documentSystemId)
            ->first();
    }

    public function translations()
    {
        return $this->hasMany(DocumentMasterTranslation::class, 'documentSystemID', 'documentSystemID');
    }

    public function translation($languageCode = null)
    {
        if (!$languageCode) {
            $languageCode = app()->getLocale() ?: 'en';
        }
        
        return $this->translations()->where('languageCode', $languageCode)->first();
    }

    public function getDocumentDescriptionAttribute($value)
    {
        $currentLanguage = app()->getLocale() ?: 'en';
        
        $translation = $this->translation($currentLanguage);
        
        if ($translation && $translation->description) {
            return $translation->description;
        }

        return $this->attributes['documentDescription'] ?? '';
    }
}
