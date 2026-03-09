<?php

namespace App\Repositories;

use App\Models\PerformaTemp;
use App\Repositories\BaseRepository;

/**
 * Class PerformaTempRepository
 * @package App\Repositories
 * @version September 10, 2018, 6:26 am UTC
 *
 * @method PerformaTemp findWithoutFail($id, $columns = ['*'])
 * @method PerformaTemp find($id, $columns = ['*'])
 * @method PerformaTemp first($columns = ['*'])
*/
class PerformaTempRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'performaMasterID',
        'myStdTitle',
        'companyID',
        'contractid',
        'performaInvoiceNo',
        'sumofsumofStandbyAmount',
        'TicketNo',
        'myTicketNo',
        'clientID',
        'performaDate',
        'performaFinanceConfirmed',
        'PerformaOpConfirmed',
        'performaFinanceConfirmedBy',
        'performaOpConfirmedDate',
        'performaFinanceConfirmedDate',
        'stdGLcode',
        'sortOrder',
        'timestamp',
        'proformaComment',
        'isDiscount',
        'discountDescription',
        'DiscountPercentage'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PerformaTemp::class;
    }
}
