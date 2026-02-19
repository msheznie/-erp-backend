<?php

namespace App\Repositories;

use App\Models\CustomerInvoiceStatusType;
use App\Repositories\BaseRepository;

/**
 * Class CustomerInvoiceStatusTypeRepository
 * @package App\Repositories
 * @version July 6, 2020, 8:53 am +04
 *
 * @method CustomerInvoiceStatusType findWithoutFail($id, $columns = ['*'])
 * @method CustomerInvoiceStatusType find($id, $columns = ['*'])
 * @method CustomerInvoiceStatusType first($columns = ['*'])
*/
class CustomerInvoiceStatusTypeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'description',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CustomerInvoiceStatusType::class;
    }
}
