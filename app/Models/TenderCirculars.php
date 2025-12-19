<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="TenderCirculars",
 *      required={""},
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
 *          property="circular_name",
 *          description="circular_name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="attachment_id",
 *          description="attachment_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="status",
 *          description="if 1 published",
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
 *          property="deleted_at",
 *          description="deleted_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="deleted_by",
 *          description="deleted_by",
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
class TenderCirculars extends Model
{

    public $table = 'srm_tender_circulars';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $timestamps = false;

    public $fillable = [
        'tender_id',
        'circular_name',
        'description',
        'attachment_id',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
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
        'tender_id' => 'integer',
        'circular_name' => 'string',
        'description' => 'string',
        'attachment_id' => 'integer',
        'status' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer',
        'company_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public function document_attachments()
    {
        return $this->hasOne('App\Models\DocumentAttachments', 'attachmentID', 'attachment_id');
    }

    public function document_amendments()
    {
        return $this->hasMany('App\Models\CircularAmendments', 'circular_id', 'id');
    }

    public function srm_circular_suppliers()
    {
        return $this->hasMany('App\Models\CircularSuppliers', 'circular_id', 'id');
    }

    public static function getTenderCircularForAmd($tender_id){
        return self::where('tender_id', $tender_id)->get();
    }

    public static function getCircularList($tender_id, $companyId)
    {
        return self::with(['document_attachments'])->where('tender_id', $tender_id)->where('company_id', $companyId);
    }
    public static function checkCircularNameExists($name, $tenderID, $companyID, $id=0){
        return self::where('circular_name', $name)
            ->when($id > 0,function ($q) use ($id) {
                $q->where('id', '!=', $id);
            })
            ->where('tender_id', $tenderID)
            ->where('company_id', $companyID)->first();
    }
}
