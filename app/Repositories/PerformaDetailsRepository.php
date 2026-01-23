<?php

namespace App\Repositories;

use App\Models\PerformaDetails;
use App\Repositories\BaseRepository;

/**
 * Class PerformaDetailsRepository
 * @package App\Repositories
 * @version August 10, 2018, 7:04 am UTC
 *
 * @method PerformaDetails findWithoutFail($id, $columns = ['*'])
 * @method PerformaDetails find($id, $columns = ['*'])
 * @method PerformaDetails first($columns = ['*'])
*/
class PerformaDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companyID',
        'serviceLine',
        'customerID',
        'contractID',
        'performaMasterID',
        'performaCode',
        'ticketNo',
        'currencyID',
        'totAmount',
        'financeGLcode',
        'invoiceSsytemCode',
        'vendorCode',
        'bankID',
        'accountID',
        'paymentPeriodDays',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PerformaDetails::class;
    }
}
