<?php

namespace App\Repositories;

use App\Models\PurchaseReturnLogistic;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class PurchaseReturnLogisticRepository
 * @package App\Repositories
 * @version December 6, 2021, 3:07 pm +04
 *
 * @method PurchaseReturnLogistic findWithoutFail($id, $columns = ['*'])
 * @method PurchaseReturnLogistic find($id, $columns = ['*'])
 * @method PurchaseReturnLogistic first($columns = ['*'])
*/
class PurchaseReturnLogisticRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'grvAutoID',
        'grvDetailID',
        'purchaseReturnID',
        'purchaseReturnDetailID',
        'logisticAmountTrans',
        'logisticAmountRpt',
        'logisticAmountLocal',
        'logisticVATAmount',
        'logisticVATAmountLocal',
        'logisticVATAmountRpt',
        'UnbilledGRVAccountSystemID',
        'supplierID',
        'supplierTransactionCurrencyID'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PurchaseReturnLogistic::class;
    }
}
