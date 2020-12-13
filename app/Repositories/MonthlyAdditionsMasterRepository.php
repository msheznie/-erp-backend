<?php

namespace App\Repositories;

use App\Models\MonthlyAdditionsMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class MonthlyAdditionsMasterRepository
 * @package App\Repositories
 * @version November 7, 2018, 7:35 am UTC
 *
 * @method MonthlyAdditionsMaster findWithoutFail($id, $columns = ['*'])
 * @method MonthlyAdditionsMaster find($id, $columns = ['*'])
 * @method MonthlyAdditionsMaster first($columns = ['*'])
*/
class MonthlyAdditionsMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'monthlyAdditionsCode',
        'serialNo',
        'documentSystemID',
        'documentID',
        'companySystemID',
        'CompanyID',
        'description',
        'currency',
        'processPeriod',
        'dateMA',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedby',
        'confirmedDate',
        'approvedYN',
        'approvedByUserSystemID',
        'approvedby',
        'approvedDate',
        'RollLevForApp_curr',
        'localCurrencyID',
        'localCurrencyExchangeRate',
        'rptCurrencyID',
        'rptCurrencyExchangeRate',
        'expenseClaimAdditionYN',
        'modifiedUserSystemID',
        'modifieduser',
        'modifiedpc',
        'createdUserSystemID',
        'createduserGroup',
        'createdpc',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return MonthlyAdditionsMaster::class;
    }

    public function getAudit($id)
    {
        return $this->with(['created_by', 'confirmed_by', 'modified_by', 'company', 'details', 'approved_by' => function ($query) {
            $query->with(['employee' => function ($q) {
                $q->with(['details.designation']);
            }])
                ->where('documentSystemID', 28);
        },'audit_trial.modified_by'])->findWithoutFail($id);
    }
}
