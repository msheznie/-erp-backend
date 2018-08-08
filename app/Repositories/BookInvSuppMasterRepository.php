<?php

namespace App\Repositories;

use App\Models\BookInvSuppMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class BookInvSuppMasterRepository
 * @package App\Repositories
 * @version August 8, 2018, 6:48 am UTC
 *
 * @method BookInvSuppMaster findWithoutFail($id, $columns = ['*'])
 * @method BookInvSuppMaster find($id, $columns = ['*'])
 * @method BookInvSuppMaster first($columns = ['*'])
*/
class BookInvSuppMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
        'serialNo',
        'companyFinanceYearID',
        'FYBiggin',
        'FYEnd',
        'companyFinancePeriodID',
        'FYPeriodDateFrom',
        'FYPeriodDateTo',
        'bookingInvCode',
        'bookingDate',
        'comments',
        'secondaryRefNo',
        'supplierID',
        'supplierGLCode',
        'supplierInvoiceNo',
        'supplierInvoiceDate',
        'supplierTransactionCurrencyID',
        'supplierTransactionCurrencyER',
        'companyReportingCurrencyID',
        'companyReportingER',
        'localCurrencyID',
        'localCurrencyER',
        'bookingAmountTrans',
        'bookingAmountLocal',
        'bookingAmountRpt',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approved',
        'approvedDate',
        'postedDate',
        'documentType',
        'timesReferred',
        'RollLevForApp_curr',
        'interCompanyTransferYN',
        'createdUserGroup',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'modifiedUser',
        'modifiedPc',
        'createdDateTime',
        'cancelYN',
        'cancelComment',
        'cancelDate',
        'canceledByEmpSystemID',
        'canceledByEmpID',
        'canceledByEmpName',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return BookInvSuppMaster::class;
    }
}
