<?php

namespace App\Repositories;

use App\Models\CustomerInvoiceUploadDetail;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CustomerInvoiceUploadDetailRepository
 * @package App\Repositories
 * @version December 3, 2023, 1:35 pm +04
 *
 * @method CustomerInvoiceUploadDetail findWithoutFail($id, $columns = ['*'])
 * @method CustomerInvoiceUploadDetail find($id, $columns = ['*'])
 * @method CustomerInvoiceUploadDetail first($columns = ['*'])
*/
class CustomerInvoiceUploadDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'customerInvoiceUploadID',
        'custInvoiceDirectID'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CustomerInvoiceUploadDetail::class;
    }
}
