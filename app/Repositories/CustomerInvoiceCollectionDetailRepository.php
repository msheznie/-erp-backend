<?php

namespace App\Repositories;

use App\Models\CustomerInvoiceCollectionDetail;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CustomerInvoiceCollectionDetailRepository
 * @package App\Repositories
 * @version December 17, 2018, 11:03 am UTC
 *
 * @method CustomerInvoiceCollectionDetail findWithoutFail($id, $columns = ['*'])
 * @method CustomerInvoiceCollectionDetail find($id, $columns = ['*'])
 * @method CustomerInvoiceCollectionDetail first($columns = ['*'])
*/
class CustomerInvoiceCollectionDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
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
     * Configure the Model
     **/
    public function model()
    {
        return CustomerInvoiceCollectionDetail::class;
    }
}
