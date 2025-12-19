<?php

namespace App\Repositories;

use App\Models\PaymentVoucherBankChargeDetails;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class PaymentVoucherBankChargeDetailsRepository
 * @package App\Repositories
 * @version November 8, 2024, 10:20 am +04
 *
 * @method PaymentVoucherBankChargeDetails findWithoutFail($id, $columns = ['*'])
 * @method PaymentVoucherBankChargeDetails find($id, $columns = ['*'])
 * @method PaymentVoucherBankChargeDetails first($columns = ['*'])
*/
class PaymentVoucherBankChargeDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'payMasterAutoID',
        'companyID',
        'companySystemID',
        'chartOfAccountSystemID',
        'glCode',
        'glCodeDescription',
        'serviceLineSystemID',
        'serviceLineCode',
        'dpAmountCurrency',
        'dpAmountCurrencyER',
        'dpAmount',
        'localCurrency',
        'localCurrencyER',
        'localAmount',
        'comRptCurrency',
        'comRptCurrencyER',
        'comRptAmount',
        'comment'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PaymentVoucherBankChargeDetails::class;
    }
}
