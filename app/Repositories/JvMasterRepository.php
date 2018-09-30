<?php

namespace App\Repositories;

use App\Models\JvMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class JvMasterRepository
 * @package App\Repositories
 * @version September 25, 2018, 7:43 am UTC
 *
 * @method JvMaster findWithoutFail($id, $columns = ['*'])
 * @method JvMaster find($id, $columns = ['*'])
 * @method JvMaster first($columns = ['*'])
*/
class JvMasterRepository extends BaseRepository
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
        'postedDate',
        'jvType',
        'isReverseAccYN',
        'timesReferred',
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
        return JvMaster::class;
    }
}
