<?php

namespace App\Repositories;

use App\Models\ErpBudgetAddition;
use App\Repositories\BaseRepository;

/**
 * Class ErpBudgetAdditionRepository
 *
 * @package App\Repositories
 * @version June 30, 2021, 9:07 am +04
 *
 * @method ErpBudgetAddition findWithoutFail($id, $columns = ['*'])
 * @method ErpBudgetAddition find($id, $columns = ['*'])
 * @method ErpBudgetAddition first($columns = ['*'])
 */
class ErpBudgetAdditionRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'templatesMasterAutoID',
        'documentSystemID',
        'documentID',
        'companySystemID',
        'companyID',
        'serialNo',
        'year',
        'additionVoucherNo',
        'createdDate',
        'comments',
        'confirmedYN',
        'confirmedDate',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByEmpName',
        'approvedYN',
        'approvedDate',
        'approvedByUserSystemID',
        'approvedEmpID',
        'approvedEmpName',
        'timesReferred',
        'RollLevForApp_curr',
        'createdDateTime',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'modifiedPc',
        'modifiedUser',
        'modifiedUserSystemID',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ErpBudgetAddition::class;
    }

    public function budgetAdditionFormListQuery($request, $input, $search = '')
    {

        $selectedCompanyId = $request['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $budgetAddition = ErpBudgetAddition::whereIn('companySystemID', $subCompanies)
            ->with('created_by', 'confirmed_by')
            ->where('documentSystemID', $input['documentId']);

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $budgetAddition = $budgetAddition->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approvedYN', $input)) {
            if (($input['approvedYN'] == 0 || $input['approvedYN'] == -1) && !is_null($input['approvedYN'])) {
                $budgetAddition = $budgetAddition->where('approvedYN', $input['approvedYN']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $budgetAddition = $budgetAddition->whereMonth('createdDateTime', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $budgetAddition = $budgetAddition->whereYear('createdDateTime', '=', $input['year']);
            }
        }

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $budgetAddition = $budgetAddition->where(function ($query) use ($search) {
                $query->where('additionVoucherNo', 'LIKE', "%{$search}%")
                    ->orWhere('comments', 'like', "%{$search}%");
            });
        }

        return $budgetAddition;
    }

    public function fetchBudgetData($id){ 
        $data = ErpBudgetAddition::with(['created_by','company'=> function ($q) {
            $q->with(['reportingcurrency']);
        }])
        ->where('id',$id)
        ->first();

        return $data;
    }

    public function getAudit($id)
    {
        return $this->with(['detail' => function ($query) {
            //$query->with('segment');
        }, 'approved_by' => function ($query) {
            $query->with('employee');
            $query->where('documentSystemID', 102);
        }, 'company','confirmed_by', 'created_by', 'modified_by','audit_trial.modified_by'])
            ->findWithoutFail($id);
    }

}
