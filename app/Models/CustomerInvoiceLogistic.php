<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="CustomerInvoiceLogistic",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="custInvoiceDirectAutoID",
 *          description="custInvoiceDirectAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="consignee_name",
 *          description="consignee_name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="consignee_contact_no",
 *          description="consignee_contact_no",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="consignee_address",
 *          description="consignee_address",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="vessel_no",
 *          description="vessel_no",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="b_ladding_no",
 *          description="b_ladding_no",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="port_of_loading",
 *          description="port_of_loading",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="port_of_discharge",
 *          description="port_of_discharge",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="no_of_container",
 *          description="no_of_container",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="delivery_payment",
 *          description="delivery_payment",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="payment_terms",
 *          description="payment_terms",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="is_deleted",
 *          description="is_deleted",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="created_by",
 *          description="created_by",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="updated_by",
 *          description="updated_by",
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
 *          property="updated_at",
 *          description="updated_at",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class CustomerInvoiceLogistic extends Model
{

    public $table = 'erp_customerinvoicelogistic';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'custInvoiceDirectAutoID',
        'consignee_name',
        'consignee_contact_no',
        'consignee_address',
        'vessel_no',
        'b_ladding_no',
        'port_of_loading',
        'port_of_discharge',
        'no_of_container',
        'delivery_payment',
        'payment_terms',
        'packing',
        'parking',
        'is_deleted',
        'created_by',
        'updated_by'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'custInvoiceDirectAutoID' => 'integer',
        'consignee_name' => 'string',
        'consignee_contact_no' => 'string',
        'consignee_address' => 'string',
        'vessel_no' => 'string',
        'b_ladding_no' => 'string',
        'port_of_loading' => 'integer',
        'port_of_discharge' => 'integer',
        'no_of_container' => 'string',
        'delivery_payment' => 'string',
        'payment_terms' => 'string',
        'parking' => 'string',
        'is_deleted' => 'boolean',
        'created_by' => 'integer',
        'updated_by' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'custInvoiceDirectAutoID' => 'required',
    ];

    public function port_of_loading()
    {
        return $this->belongsTo('App\Models\PortMaster', 'port_of_loading', 'id');
    }

    public function port_of_discharge()
    {
        return $this->belongsTo('App\Models\PortMaster', 'port_of_discharge', 'id');
    }

    
}
