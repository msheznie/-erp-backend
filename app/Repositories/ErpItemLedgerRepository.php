<?php

namespace App\Repositories;

use App\Models\ErpItemLedger;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ErpItemLedgerRepository
 * @package App\Repositories
 * @version May 30, 2018, 10:37 am UTC
 *
 * @method ErpItemLedger findWithoutFail($id, $columns = ['*'])
 * @method ErpItemLedger find($id, $columns = ['*'])
 * @method ErpItemLedger first($columns = ['*'])
*/
class ErpItemLedgerRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineCode',
        'documentSystemID',
        'documentID',
        'documentSystemCode',
        'documentCode',
        'referenceNumber',
        'wareHouseSystemCode',
        'itemSystemCode',
        'itemPrimaryCode',
        'itemDescription',
        'unitOfMeasure',
        'inOutQty',
        'wacLocalCurrencyID',
        'wacLocal',
        'wacRptCurrencyID',
        'wacRpt',
        'comments',
        'transactionDate',
        'fromDamagedTransactionYN',
        'createdUserSystemID',
        'createdUserID',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ErpItemLedger::class;
    }
}
