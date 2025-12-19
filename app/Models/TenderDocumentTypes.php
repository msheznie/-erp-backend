<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="TenderDocumentTypes",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="document_type",
 *          description="document_type",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="srm_action",
 *          description="srm_action",
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
 *      ),
 *      @SWG\Property(
 *          property="company_id",
 *          description="company_id",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class TenderDocumentTypes extends Model
{

    public $table = 'srm_tender_document_types';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $timestamps = false;

    protected $appends = ['attachments'];

    public $fillable = [
        'document_type',
        'srm_action',
        'system_generated',
        'sort_order',
        'created_by',
        'updated_by',
        'company_id',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'document_type' => 'string',
        'srm_action' => 'integer',
        'system_generated' => 'integer',
        'sort_order' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'company_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function attachments()
    {
        return $this->hasMany('App\Models\DocumentAttachments', 'attachmentType', 'id');
    }
    public function attachment_logs()
    {
        return $this->hasMany('App\Models\DocumentAttachmentsEditLog', 'attachmentType', 'id');
    }

    public function tender_document_type_assign(){ 
        return $this->hasOne('App\Models\TenderDocumentTypeAssign', 'document_type_id', 'id');
    }

    public function TenderDocumentTypeAssign(){
        return $this->hasMany('App\Models\TenderDocumentTypeAssign', 'document_type_id', 'id');
    }

    public static function getSortOrder()
    {
        $sortOrder = TenderDocumentTypes::orderByDesc('sort_order')
            ->where('system_generated', 0)
            ->value('sort_order') ?? 0;

        return $sortOrder += 1;
    }

    public static function getTenderDocumentTypes($input)
    {

        return TenderDocumentTypes::select('id', 'document_type', 'system_generated', 'srm_action', 'sort_order', 'company_id')
            ->with(['attachments' => function ($q) use ($input) {
                $q->select('attachmentType', 'documentSystemID', 'documentSystemCode', 'companySystemID')
                    ->where('documentSystemID', 108)
                    ->where('companySystemID', $input['companyId']);
            }, 'TenderDocumentTypeAssign' => function ($q2) {
                $q2->select('id', 'document_type_id');
            }])->orderBy('sort_order', 'asc');
    }
    public function getAttachmentsAttribute()
    {
        if ($this->relationLoaded('attachment_logs')) {
            return $this->attachment_logs;
        }

        return $this->attachments()->get(); // fallback, if needed
    }
    // In TenderDocumentTypes.php

    public static function getFilteredDocumentTypes(array $notInArray, $employeeCompanyID, $tenderPublished, $isRequestForEdit, $requestConfirmed)
    {
        $query = self::whereNotIn('id', $notInArray);

        if ($tenderPublished) {
            if ($isRequestForEdit && !$requestConfirmed) {
                return $query->get();
            }

            if (!$isRequestForEdit || $requestConfirmed) {
                return $query->where('id', 3)->get();
            }
        }
        return $query->where('company_id', $employeeCompanyID)->get();
    }
    public static function getTenderAttachmentTypes($tenderMasterId, $companySystemID, $assignDocumentTypes, $isRfx, $editOrAmend, $versionID)
    {
        $documentSystemID = $isRfx ? 113 : 108;

        return self::select('id', 'document_type', 'system_generated', 'srm_action', 'sort_order', 'company_id')
            ->when(!$editOrAmend, function ($q) use ($tenderMasterId, $companySystemID, $documentSystemID){
                $q->with(['attachments' => function ($query) use ($tenderMasterId, $companySystemID, $documentSystemID) {
                    $query->select('attachmentType','documentSystemID', 'documentSystemCode','companySystemID')
                        ->where('documentSystemCode', $tenderMasterId)
                        ->where('companySystemID', $companySystemID)
                        ->where('documentSystemID', $documentSystemID);
                }]);
            })
            ->when($editOrAmend, function ($q) use ($tenderMasterId, $companySystemID, $documentSystemID, $versionID){
                $q->with(['attachment_logs' => function ($query) use ($tenderMasterId, $companySystemID, $documentSystemID, $versionID) {
                    $query->select('attachmentType','documentSystemID', 'documentSystemCode','companySystemID')
                        ->where('documentSystemCode', $tenderMasterId)
                        ->where('version_id', $versionID)
                        ->where('is_deleted', 0)
                        ->where('companySystemID', $companySystemID)
                        ->where('documentSystemID', $documentSystemID);
                }]);
            })
            ->where(function ($query) use ($assignDocumentTypes, $companySystemID) {
                $query->whereIn('id', $assignDocumentTypes)
                    ->where('company_id', $companySystemID);
            })
            ->orWhere('system_generated', 1)
            ->when(!in_array(3, $assignDocumentTypes), function ($query) {
                $query->where('id', '!=', 3);
            })
            ->orderBy('sort_order')
            ->get();
    }

}
