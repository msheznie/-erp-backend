<?php

namespace App\Repositories;

use App\Models\UploadCustomerInvoice;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class UploadCustomerInvoiceRepository
 * @package App\Repositories
 * @version November 22, 2023, 4:15 pm +04
 *
 * @method UploadCustomerInvoice findWithoutFail($id, $columns = ['*'])
 * @method UploadCustomerInvoice find($id, $columns = ['*'])
 * @method UploadCustomerInvoice first($columns = ['*'])
*/
class UploadCustomerInvoiceRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'uploadComment',
        'uploadedDate',
        'uploadedBy',
        'uploadStatus',
        'counter',
        'companySystemID'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return UploadCustomerInvoice::class;
    }
}
