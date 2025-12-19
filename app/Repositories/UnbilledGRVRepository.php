<?php

namespace App\Repositories;

use App\Models\UnbilledGRV;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class UnbilledGRVRepository
 * @package App\Repositories
 * @version August 30, 2018, 8:00 am UTC
 *
 * @method UnbilledGRV findWithoutFail($id, $columns = ['*'])
 * @method UnbilledGRV find($id, $columns = ['*'])
 * @method UnbilledGRV first($columns = ['*'])
*/
class UnbilledGRVRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companyID',
        'supplierID',
        'purchaseOrderID',
        'grvAutoID',
        'grvDate',
        'supplierTransactionCurrencyID',
        'supplierTransactionCurrencyER',
        'companyReportingCurrencyID',
        'companyReportingER',
        'localCurrencyID',
        'localCurrencyER',
        'totTransactionAmount',
        'totLocalAmount',
        'totRptAmount',
        'isAddon',
        'grvType',
        'isReturn',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return UnbilledGRV::class;
    }
}
