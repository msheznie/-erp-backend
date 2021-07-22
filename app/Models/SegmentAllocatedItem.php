<?php

namespace App\Models;

use Eloquent as Model;
use Awobaz\Compoships\Compoships;
/**
 * @SWG\Definition(
 *      definition="SegmentAllocatedItem",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
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
 *          property="documentMasterAutoID",
 *          description="documentMasterAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentDetailAutoID",
 *          description="documentDetailAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="detailQty",
 *          description="detailQty",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="allocatedQty",
 *          description="allocatedQty",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="pulledDocumentSystemID",
 *          description="pulledDocumentSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="pulledDocumentDetailID",
 *          description="pulledDocumentDetailID",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class SegmentAllocatedItem extends Model
{

    public $table = 'segment_allocated_items';
    
    const CREATED_AT = null;
    const UPDATED_AT = null;
    public $timestamps = false;

    protected $primaryKey ='id';

    public $fillable = [
        'documentSystemID',
        'documentMasterAutoID',
        'documentDetailAutoID',
        'detailQty',
        'copiedQty',
        'allocatedQty',
        'pulledDocumentSystemID',
        'serviceLineSystemID',
        'pulledDocumentDetailID'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'documentSystemID' => 'integer',
        'documentMasterAutoID' => 'integer',
        'serviceLineSystemID' => 'integer',
        'documentDetailAutoID' => 'integer',
        'detailQty' => 'float',
        'copiedQty' => 'float',
        'allocatedQty' => 'float',
        'pulledDocumentSystemID' => 'integer',
        'pulledDocumentDetailID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function segment(){
        return $this->belongsTo('App\Models\SegmentMaster','serviceLineSystemID','serviceLineSystemID');
    }
}
