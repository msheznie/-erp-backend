<?php

namespace App\Repositories;

use App\Models\JvMasterReferredback;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class JvMasterReferredbackRepository
 * @package App\Repositories
 * @version December 5, 2018, 5:31 am UTC
 *
 * @method JvMasterReferredback findWithoutFail($id, $columns = ['*'])
 * @method JvMasterReferredback find($id, $columns = ['*'])
 * @method JvMasterReferredback first($columns = ['*'])
*/
class JvMasterReferredbackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'jvMasterAutoId',
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
        'JVcode',
        'JVdate',
        'recurringjvMasterAutoId',
        'recurringMonth',
        'recurringYear',
        'JVNarration',
        'currencyID',
        'currencyER',
        'rptCurrencyID',
        'rptCurrencyER',
        'empID',
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
        'jvType',
        'type',
        'isReverseAccYN',
        'refferedBackYN',
        'timesReferred',
        'RollLevForApp_curr',
        'isRelatedPartyYN',
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
        return JvMasterReferredback::class;
    }
}
