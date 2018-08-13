<?php

namespace App\Repositories;

use App\Models\DirectInvoiceDetails;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DirectInvoiceDetailsRepository
 * @package App\Repositories
 * @version August 9, 2018, 6:40 am UTC
 *
 * @method DirectInvoiceDetails findWithoutFail($id, $columns = ['*'])
 * @method DirectInvoiceDetails find($id, $columns = ['*'])
 * @method DirectInvoiceDetails first($columns = ['*'])
*/
class DirectInvoiceDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'directInvoiceAutoID',
        'companyID',
        'serviceLineCode',
        'glCode',
        'glCodeDes',
        'comments',
        'percentage',
        'DIAmountCurrency',
        'DIAmountCurrencyER',
        'DIAmount',
        'localCurrency',
        'localCurrencyER',
        'localAmount',
        'comRptCurrency',
        'comRptCurrencyER',
        'comRptAmount',
        'budgetYear',
        'isExtraAddon',
        'timesReferred',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DirectInvoiceDetails::class;
    }
}
