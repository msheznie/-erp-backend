<?php

namespace App\Repositories;

use App\Models\PerformaMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class PerformaMasterRepository
 * @package App\Repositories
 * @version August 15, 2018, 9:11 am UTC
 *
 * @method PerformaMaster findWithoutFail($id, $columns = ['*'])
 * @method PerformaMaster find($id, $columns = ['*'])
 * @method PerformaMaster first($columns = ['*'])
*/
class PerformaMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'PerformaInvoiceNo',
        'performaSerialNO',
        'PerformaCode',
        'companyID',
        'serviceLine',
        'clientID',
        'contractID',
        'performaDate',
        'createdUserID',
        'modifiedUserID',
        'performaStatus',
        'PerformaOpConfirmed',
        'performaOpConfirmedBy',
        'performaOpConfirmedDate',
        'PerformaFinanceConfirmed',
        'performaFinanceConfirmedBy',
        'performaFinanceConfirmedDate',
        'performaValue',
        'ticketNo',
        'bankID',
        'accountID',
        'paymentInDaysForJob',
        'custInvNoModified',
        'isPerformaOnEditRental',
        'isRefBackBillingYN',
        'refBackBillingBy',
        'refBackBillingDate',
        'isRefBackOPYN',
        'refBackOPby',
        'refBackOpDate',
        'refBillingComment',
        'refOpComment',
        'clientAppPerformaType',
        'clientapprovedDate',
        'clientapprovedBy',
        'performaSentToHO',
        'performaSentToHODate',
        'performaSentToHOEmpID',
        'lotSystemAutoID',
        'lotNumber',
        'performaReceivedByEmpID',
        'performaReceivedByDate',
        'submittedToClientDate',
        'submittedToClientByEmpID',
        'receivedFromClientDate',
        'reSubmittedDate',
        'approvedByClientDate',
        'timeStamp',
        'isAccrualYN',
        'isCanceledYN',
        'serviceCompanyID'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PerformaMaster::class;
    }
}
