<?php

namespace App\Repositories;

use App\Models\InterCompanyStockTransfer;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class InterCompanyStockTransferRepository
 * @package App\Repositories
 * @version February 3, 2022, 10:48 am +04
 *
 * @method InterCompanyStockTransfer findWithoutFail($id, $columns = ['*'])
 * @method InterCompanyStockTransfer find($id, $columns = ['*'])
 * @method InterCompanyStockTransfer first($columns = ['*'])
*/
class InterCompanyStockTransferRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'stockTransferID',
        'customerInvoiceID',
        'stockReceiveID',
        'supplierInvoiceID'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return InterCompanyStockTransfer::class;
    }
}
