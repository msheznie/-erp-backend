<?php

namespace App\Repositories;

use App\Models\CreditNote;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CreditNoteRepository
 * @package App\Repositories
 * @version August 21, 2018, 9:53 am UTC
 *
 * @method CreditNote findWithoutFail($id, $columns = ['*'])
 * @method CreditNote find($id, $columns = ['*'])
 * @method CreditNote first($columns = ['*'])
*/
class CreditNoteRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'documentSystemiD',
        'documentID',
        'serialNo',
        'companyFinanceYearID',
        'FYBiggin',
        'FYEnd',
        'companyFinancePeriodID',
        'FYPeriodDateFrom',
        'FYPeriodDateTo',
        'creditNoteCode',
        'creditNoteDate',
        'comments',
        'customerID',
        'customerGLCodeSystemID',
        'customerGLCode',
        'customerCurrencyID',
        'customerCurrencyER',
        'companyReportingCurrencyID',
        'companyReportingER',
        'localCurrencyID',
        'localCurrencyER',
        'creditAmountTrans',
        'creditAmountLocal',
        'creditAmountRpt',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approved',
        'approvedDate',
        'postedDate',
        'secondaryLogoCompID',
        'secondaryLogo',
        'matchInvoice',
        'matchingConfirmedYN',
        'matchingConfirmedByEmpSystemID',
        'matchingConfirmedByEmpID',
        'matchingConfirmedByName',
        'matchingConfirmedDate',
        'documentType',
        'timesReferred',
        'RollLevForApp_curr',
        'createdUserGroup',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'modifiedUserSystemID',
        'modifiedUser',
        'modifiedPc',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CreditNote::class;
    }

    function getAudit($id)
    {
        $creditNote = $this->with(['company', 'customer', 'createduser','currency', 'local_currency','approved_by' => function ($query) {
            $query->with('employee.details.designation')
                ->where('documentSystemID', 19);
        }, 'details'=>function($query){
            $query->with('segment');
        }
        ])->findWithoutFail($id);

        return $creditNote;

    }
}
