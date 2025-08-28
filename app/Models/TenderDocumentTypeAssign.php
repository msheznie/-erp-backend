<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="TenderDocumentTypeAssign",
 *      required={""},
 *      @SWG\Property(
 *          property="company_id",
 *          description="company_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="created_at",
 *          description="created_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="created_by",
 *          description="created_by",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="document_type_id",
 *          description="document_type_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="tender_id",
 *          description="tender_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="updated_by",
 *          description="updated_by",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class TenderDocumentTypeAssign extends Model
{

    public $table = 'srm_tender_document_type_assign';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'company_id',
        'created_by',
        'document_type_id',
        'tender_id',
        'updated_by'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'company_id' => 'integer',
        'created_by' => 'integer',
        'document_type_id' => 'integer',
        'id' => 'integer',
        'tender_id' => 'integer',
        'updated_by' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'company_id' => 'required',
        'created_at' => 'required',
        'created_by' => 'required',
        'document_type_id' => 'required',
        'tender_id' => 'required',
        'updated_at' => 'required',
        'updated_by' => 'required'
    ];

    public function document_type()
    {
        return $this->hasOne('App\Models\TenderDocumentTypes', 'id', 'document_type_id');
    }

    public static function getTenderDocumentTypeForAmd($tender_id){
        return self::where('tender_id', $tender_id)->get();
    }

    public static function getTenderDocumentTypeAssign($tenderMasterID){
        return self::with(['document_type'])->where('tender_id', $tenderMasterID)->get();
    }
    public static function getTenderDocumentTypeAssigned($tender_id, $doc_type_id, $company_id){
        return self::where('tender_id', $tender_id)
            ->where('document_type_id', $doc_type_id)
            ->where('company_id', $company_id)
            ->first();
    }

    public static function getTenderDocumentType($tenderId)
    {
        return TenderDocumentTypeAssign::where('tender_id',$tenderId)->get();
    }
}
