<?php

namespace App\Repositories;

use App\Models\StockReceive;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class StockReceiveRepository
 * @package App\Repositories
 * @version July 23, 2018, 4:46 am UTC
 *
 * @method StockReceive findWithoutFail($id, $columns = ['*'])
 * @method StockReceive find($id, $columns = ['*'])
 * @method StockReceive first($columns = ['*'])
 */
class StockReceiveRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineCode',
        'companyFinanceYearID',
        'companyFinancePeriodID',
        'FYBiggin',
        'FYEnd',
        'documentSystemID',
        'documentID',
        'serialNo',
        'stockReceiveCode',
        'refNo',
        'receivedDate',
        'comment',
        'companyFromSystemID',
        'companyFrom',
        'companyToSystemID',
        'companyTo',
        'locationTo',
        'locationFrom',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approved',
        'approvedDate',
        'postedDate',
        'timesReferred',
        'interCompanyTransferYN',
        'RollLevForApp_curr',
        'createdDateTime',
        'createdUserGroup',
        'createdPCID',
        'createdUserSystemID',
        'createdUserID',
        'modifiedUserSystemID',
        'modifiedUser',
        'modifiedPc',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return StockReceive::class;
    }


    public function getAudit($id)
    {
        return $this->with(['created_by', 'confirmed_by','company','location_to_by', 'location_from_by', 'details' => function ($q) {
            $q->with(['unit_by']);
        }, 'modified_by', 'approved_by' => function ($query) {
            $query->with('employee.details.designation')
                ->where('documentSystemID', 10);
        }])->findWithoutFail($id);
    }
}
