<?php

namespace App\Repositories;

use App\Models\UnbilledGrvGroupBy;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class UnbilledGrvGroupByRepository
 * @package App\Repositories
 * @version July 24, 2018, 4:37 am UTC
 *
 * @method UnbilledGrvGroupBy findWithoutFail($id, $columns = ['*'])
 * @method UnbilledGrvGroupBy find($id, $columns = ['*'])
 * @method UnbilledGrvGroupBy first($columns = ['*'])
*/
class UnbilledGrvGroupByRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
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
        'selectedForBooking',
        'fullyBooked',
        'grvType',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return UnbilledGrvGroupBy::class;
    }
}
