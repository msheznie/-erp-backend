<?php

namespace App\Models;
use Awobaz\Compoships\Compoships;
use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="TenderBidClarifications",
 *      required={""},
 *      @SWG\Property(
 *          property="comment",
 *          description="comment",
 *          type="string"
 *      ),
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
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="is_answered",
 *          description="is_answered",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="is_public",
 *          description="is_public",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="parent_id",
 *          description="parent_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="post",
 *          description="post",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="supplier_id",
 *          description="supplier_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="tender_master_id",
 *          description="tender_master_id",
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
 *          property="user_id",
 *          description="user_id",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class TenderBidClarifications extends Model
{
    use Compoships;
    public $table = 'srm_tender_pre_bid_clarifications';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [  
        'company_id',
        'created_by',
        'is_answered',
        'is_public',
        'parent_id',
        'post',
        'supplier_id',
        'tender_master_id',
        'updated_by',
        'user_id',
        'posted_by_type', 
        'document_system_id',
        'document_id',
        'is_closed',
        'is_anonymous',
        'is_checked'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [ 
        'company_id' => 'integer',
        'created_by' => 'integer',
        'id' => 'integer',
        'is_answered' => 'integer',
        'is_public' => 'integer',
        'parent_id' => 'integer',
        'post' => 'string',
        'supplier_id' => 'integer',
        'tender_master_id' => 'integer',
        'updated_by' => 'integer',
        'user_id' => 'integer',
        'posted_by_type' => 'integer',
        'is_closed' => 'integer',
        'is_anonymous' => 'boolean',
        'is_checked' => 'boolean'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'created_by' => 'required',
        'is_answered' => 'required',
        'is_public' => 'required',
        'parent_id' => 'required',
        'updated_by' => 'required'
    ]; 
    public function supplier(){ 
        return $this->hasOne('App\Models\SupplierRegistrationLink', 'id', 'supplier_id');
    } 
    public function employee()
    {
        return $this->hasOne('App\Models\Employee', 'employeeSystemID', 'user_id');
    }
    public function attachment()
    {
        return $this->hasMany('App\Models\DocumentAttachments',['documentSystemID', 'documentSystemCode'], ['document_system_id', 'id']);
    }

    public function attachments()
    {
        return $this->hasMany('App\Models\DocumentAttachments',['documentSystemID', 'documentSystemCode'], ['document_system_id', 'id']);
    }

    public function replies()
    {
        return $this->hasMany('App\Models\TenderBidClarifications','parent_id', 'id')->with('replies');
    }
    public function tender()
    {
        return $this->hasOne('App\Models\TenderMaster', 'id', 'tender_master_id');
    }
    public static function getPreBidTenderID($preBidId){
        $bidData = self::select('tender_master_id')->where('id', $preBidId)->first();
        return $bidData->tender_master_id ?? 0;
    }
    public static function checkAccessForTenderBid($preBidId, $supplierRegId)
    {
        $claData = self::select('supplier_id', 'is_public')
            ->where('id', $preBidId)
            ->first();

        if (!$claData) {
            return false;
        }

        return $claData->supplier_id == $supplierRegId || $claData->is_public == 1;
    }

    public static function checkSupplierBidClarification($tenderId, $supplierID){
        return self::where('tender_master_id', $tenderId)->where('supplier_id', $supplierID)->exists();
    }
}
