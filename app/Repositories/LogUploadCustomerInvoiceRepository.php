<?php

namespace App\Repositories;

use App\Models\LogUploadCustomerInvoice;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class LogUploadCustomerInvoiceRepository
 * @package App\Repositories
 * @version November 22, 2023, 11:52 pm +04
 *
 * @method LogUploadCustomerInvoice findWithoutFail($id, $columns = ['*'])
 * @method LogUploadCustomerInvoice find($id, $columns = ['*'])
 * @method LogUploadCustomerInvoice first($columns = ['*'])
*/
class LogUploadCustomerInvoiceRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'customerInvoiceUploadID',
        'companySystemID',
        'is_failed',
        'log_message'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return LogUploadCustomerInvoice::class;
    }
}
