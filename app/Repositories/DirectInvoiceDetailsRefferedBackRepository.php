<?php

namespace App\Repositories;

use App\Models\DirectInvoiceDetailsRefferedBack;
use App\Repositories\BaseRepository;

/**
 * Class DirectInvoiceDetailsRefferedBackRepository
 * @package App\Repositories
 * @version September 27, 2018, 10:35 am UTC
 *
 * @method DirectInvoiceDetailsRefferedBack findWithoutFail($id, $columns = ['*'])
 * @method DirectInvoiceDetailsRefferedBack find($id, $columns = ['*'])
 * @method DirectInvoiceDetailsRefferedBack first($columns = ['*'])
*/
class DirectInvoiceDetailsRefferedBackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'directInvoiceDetailsID',
        'directInvoiceAutoID',
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineCode',
        'chartOfAccountSystemID',
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
        return DirectInvoiceDetailsRefferedBack::class;
    }
}
