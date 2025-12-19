<?php

namespace App\Repositories;

use App\Models\BudgetTransferForm;
use InfyOm\Generator\Common\BaseRepository;
use App\helper\StatusService;

/**
 * Class BudgetTransferFormRepository
 * @package App\Repositories
 * @version October 17, 2018, 12:24 pm UTC
 *
 * @method BudgetTransferForm findWithoutFail($id, $columns = ['*'])
 * @method BudgetTransferForm find($id, $columns = ['*'])
 * @method BudgetTransferForm first($columns = ['*'])
*/
class BudgetTransferFormRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'documentSystemID',
        'documentID',
        'companySystemID',
        'companyID',
        'serialNo',
        'year',
        'transferVoucherNo',
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
        'RollLevForApp_curr',
        'createdDateTime',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return BudgetTransferForm::class;
    }

    public function getAudit($id)
    {
        return $this->with(['detail' => function ($query) {
            //$query->with('segment');
        }, 'approved_by' => function ($query) {
            $query->with('employee');
            $query->where('documentSystemID', 46);
        }, 'company','confirmed_by', 'created_by', 'modified_by','audit_trial.modified_by'])
            ->findWithoutFail($id);
    }

    public function budgetTransferFormListQuery($request, $input, $search = '') {

        $selectedCompanyId = $request['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $budgetTransfer = BudgetTransferForm::whereIn('companySystemID', $subCompanies)
            ->with('created_by','confirmed_by')
            ->where('documentSystemID', $input['documentId']);

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $budgetTransfer = $budgetTransfer->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approvedYN', $input)) {
            if (($input['approvedYN'] == 0 || $input['approvedYN'] == -1) && !is_null($input['approvedYN'])) {
                $budgetTransfer = $budgetTransfer->where('approvedYN', $input['approvedYN']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $budgetTransfer = $budgetTransfer->whereMonth('createdDateTime', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $budgetTransfer = $budgetTransfer->whereYear('createdDateTime', '=', $input['year']);
            }
        }

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $budgetTransfer = $budgetTransfer->where(function ($query) use ($search) {
                $query->where('transferVoucherNo', 'LIKE', "%{$search}%")
                      ->orWhere('comments', 'like', "%{$search}%");
            });
        }

        return $budgetTransfer;
    }

    public function setExportExcelData($dataSet) {

        $dataSet = $dataSet->get();
        if (count($dataSet) > 0) {
            $x = 0;

            foreach ($dataSet as $val) {
                $data[$x][trans('custom.created_date')] = \Helper::dateFormat($val->createdDateTime);
                $data[$x][trans('custom.transfer_document_code')] = $val->transferVoucherNo;
                $data[$x][trans('custom.narration')] = $val->comments;
                $data[$x][trans('custom.submitted_by')] = $val->confirmed_by? $val->confirmed_by->empName : '';
                $data[$x][trans('custom.status')] = StatusService::getStatus($val->CancelledYN, NULL, $val->confirmedYN, $val->approvedYN, $val->timesReferred);

                $x++;
            }
        } else {
            $data = array();
        }

        return $data;
    }
}
