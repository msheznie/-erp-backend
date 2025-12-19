<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="CustomerInvoiceCollectionDetail",
 *      required={""},
 *      @SWG\Property(
 *          property="collectionDetailID",
 *          description="collectionDetailID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customerInvoiceID",
 *          description="customerInvoiceID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="invoiceStatusTypeID",
 *          description="invoiceStatusTypeID",
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
 *          property="companyID",
 *          description="companyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="comments",
 *          description="comments",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="actionRequired",
 *          description="actionRequired",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserGroup",
 *          description="createdUserGroup",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserSystemID",
 *          description="createdUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdPcID",
 *          description="createdPcID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserSystemID",
 *          description="modifiedUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUser",
 *          description="modifiedUser",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedPc",
 *          description="modifiedPc",
 *          type="string"
 *      )
 * )
 */
class CustomerInvoiceCollectionDetail extends Model
{

    public $table = 'erp_customerinvoicecollectiondetail';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey  = 'collectionDetailID';

    public $fillable = [
        'customerInvoiceID',
        'invoiceStatusTypeID',
        'companySystemID',
        'companyID',
        'collectionDate',
        'comments',
        'actionRequired',
        'createdDateTime',
        'createdUserGroup',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'modifiedUserSystemID',
        'modifiedUser',
        'modifiedPc',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'collectionDetailID' => 'integer',
        'customerInvoiceID' => 'integer',
        'invoiceStatusTypeID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'comments' => 'string',
        'actionRequired' => 'string',
        'createdUserGroup' => 'string',
        'createdUserSystemID' => 'integer',
        'createdUserID' => 'string',
        'createdPcID' => 'string',
        'modifiedUserSystemID' => 'integer',
        'modifiedUser' => 'string',
        'modifiedPc' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function modified_by()
    {
        return $this->belongsTo('App\Models\Employee', 'modifiedUserSystemID', 'employeeSystemID');
    }

    
}
