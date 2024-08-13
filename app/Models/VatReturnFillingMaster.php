<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="VatReturnFillingMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companySystemID",
 *          description="companySystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentSystemID",
 *          description="documentSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="date",
 *          description="date",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="comment",
 *          description="comment",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="created_at",
 *          description="created_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="confirmedYN",
 *          description="confirmedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="confirmedDate",
 *          description="confirmedDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="confirmedByEmpSystemID",
 *          description="confirmedByEmpSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="confirmedByEmpID",
 *          description="confirmedByEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="confirmedByEmpName",
 *          description="confirmedByEmpName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="approvedYN",
 *          description="approvedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="approvedDate",
 *          description="approvedDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="approvedByUserSystemID",
 *          description="approvedByUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="approvedEmpID",
 *          description="approvedEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="refferedBackYN",
 *          description="refferedBackYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="timesReferred",
 *          description="timesReferred",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="RollLevForApp_curr",
 *          description="RollLevForApp_curr",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class VatReturnFillingMaster extends Model
{

    public $table = 'vat_return_filling_master';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'companySystemID',
        'returnFillingCode',
        'documentSystemID',
        'date',
        'comment',
        'serialNo',
        'confirmedYN',
        'confirmedDate',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByEmpName',
        'approvedYN',
        'approvedDate',
        'approvedByUserSystemID',
        'approvedEmpID',
        'refferedBackYN',
        'timesReferred',
        'RollLevForApp_curr',
        'masterDocumentAutoID',
        'masterDocumentTypeID' // document master ID
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'companySystemID' => 'integer',
        'documentSystemID' => 'integer',
        'serialNo' => 'integer',
        'date' => 'datetime',
        'comment' => 'string',
        'confirmedYN' => 'integer',
        'confirmedDate' => 'datetime',
        'confirmedByEmpSystemID' => 'integer',
        'confirmedByEmpID' => 'string',
        'confirmedByEmpName' => 'string',
        'returnFillingCode' => 'string',
        'approvedYN' => 'integer',
        'approvedDate' => 'datetime',
        'approvedByUserSystemID' => 'integer',
        'approvedEmpID' => 'string',
        'refferedBackYN' => 'integer',
        'timesReferred' => 'integer',
        'RollLevForApp_curr' => 'integer',
        'masterDocumentAutoID' => 'integer',
        'masterDocumentTypeID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function filled_master_categories(){
        return $this->hasMany('App\Models\VatReturnFilledCategory', 'vatReturnFillingID','id');
    }

     public function confirmed_by()
    {
        return $this->belongsTo('App\Models\Employee', 'confirmedByEmpSystemID', 'employeeSystemID');
    }

    public function invoice()
    {
        return $this->belongsTo('App\Models\BookInvSuppMaster','masterDocumentAutoID','bookingSuppMasInvAutoID');
    }

    public function scopeGeneratedDocument()
    {

        switch ($this->masterDocumentTypeID)
        {
            case 11 :
                return $this->belongsTo('App\Models\BookInvSuppMaster', 'masterDocumentAutoID','bookingSuppMasInvAutoID');
                break;
            case 15:
                return $this->belongsTo('App\Models\DebitNote', 'masterDocumentAutoID','debitNoteAutoID');
                break;

        }
    }

    public function attachGeneratedDocument($documentMasterID, $documentTypeID)
    {
        $this->masterDocumentAutoID = $documentMasterID;
        $this->masterDocumentTypeID = $documentTypeID;
        $this->save();
    }

    public function scopeIsDocumentGenerated()
    {
        return VatReturnFillingMaster::where('id',$this->id)->whereNotNull('masterDocumentAutoID')->exists();
    }

    public function scopeIsPreviousVRFHasDocument()
    {
        $prvVatReturnFillingMaster = VatReturnFillingMaster::where('id','<',$this->id)->where('approvedYN',-1)->where('companySystemID', $this->companySystemID)->orderBy('id','desc')->first();

        return isset($prvVatReturnFillingMaster) && !is_null($prvVatReturnFillingMaster->masterDocumentAutoID);
    }

}
