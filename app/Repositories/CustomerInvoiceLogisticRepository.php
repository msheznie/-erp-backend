<?php

namespace App\Repositories;

use App\Models\CustomerInvoiceLogistic;
use App\Repositories\BaseRepository;

/**
 * Class CustomerInvoiceLogisticRepository
 * @package App\Repositories
 * @version May 30, 2022, 10:32 am +04
 *
 * @method CustomerInvoiceLogistic findWithoutFail($id, $columns = ['*'])
 * @method CustomerInvoiceLogistic find($id, $columns = ['*'])
 * @method CustomerInvoiceLogistic first($columns = ['*'])
*/
class CustomerInvoiceLogisticRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
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
        'is_deleted',
        'created_by',
        'updated_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CustomerInvoiceLogistic::class;
    }
}
