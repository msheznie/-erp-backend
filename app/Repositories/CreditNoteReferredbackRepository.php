<?php

namespace App\Repositories;

use App\Models\CreditNoteReferredback;
use App\Repositories\BaseRepository;

/**
 * Class CreditNoteReferredbackRepository
 * @package App\Repositories
 * @version November 26, 2018, 10:58 am UTC
 *
 * @method CreditNoteReferredback findWithoutFail($id, $columns = ['*'])
 * @method CreditNoteReferredback find($id, $columns = ['*'])
 * @method CreditNoteReferredback first($columns = ['*'])
*/
class CreditNoteReferredbackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'creditNoteAutoID',
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
        'approvedByUserID',
        'approvedByUserSystemID',
        'postedDate',
        'secondaryLogoCompanySystemID',
        'secondaryLogoCompID',
        'secondaryLogo',
        'matchInvoice',
        'matchingConfirmedYN',
        'matchingConfirmedByEmpSystemID',
        'matchingConfirmedByEmpID',
        'matchingConfirmedByName',
        'matchingConfirmedDate',
        'documentType',
        'refferedBackYN',
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
        'createdDateAndTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CreditNoteReferredback::class;
    }
}
