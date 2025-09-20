<?php

namespace App\Repositories;

use App\Models\BankReconciliation;
use InfyOm\Generator\Common\BaseRepository;
use App\helper\StatusService;
use Carbon\Carbon;


/**
 * Class BankReconciliationRepository
 * @package App\Repositories
 * @version September 18, 2018, 4:11 am UTC
 *
 * @method BankReconciliation findWithoutFail($id, $columns = ['*'])
 * @method BankReconciliation find($id, $columns = ['*'])
 * @method BankReconciliation first($columns = ['*'])
*/
class BankReconciliationRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'documentSystemID',
        'documentID',
        'companySystemID',
        'companyID',
        'bankGLAutoID',
        'month',
        'bankRecPrimaryCode',
        'year',
        'bankRecAsOf',
        'openingBalance',
        'closingBalance',
        'description',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approvedYN',
        'approvedDate',
        'approvedByUserID',
        'approvedByUserSystemID',
        'RollLevForApp_curr',
        'createdPcID',
        'createdUserSystemID',
        'createdUserID',
        'modifiedPc',
        'modifiedUserSystemID',
        'modifiedUser',
        'createdDateTime',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return BankReconciliation::class;
    }

    public function getAudit($id)
    {
        return $this->with(['bank_account.currency', 'confirmed_by', 'company', 'month','month_by' ,'approved_by' => function ($query) {
            $query->with(['employee' => function ($q) {
                $q->with(['details.designation']);
            }])
                ->where('documentSystemID', 62);
        }])->findWithoutFail($id);
    }

    public function bankReconciliationListQuery($request, $input, $search = '' ,$bankmasterAutoID) {

        $selectedCompanyId = $request['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $bankReconciliation = BankReconciliation::whereIn('companySystemID', $subCompanies)
                                                ->with(['month', 'created_by', 'bank_account']);

        if (isset($input['month']) && $input['month'] != null) {
            $month = Carbon::parse($input['month'])->format('m');

            $bankReconciliation = $bankReconciliation->where('month', $month);
        }


        if (isset($input['forReview']) && $input['forReview']) {
            $bankReconciliation = $bankReconciliation->where('confirmedYN', 1);
        }


        if (isset($input['year']) && $input['year'] != null) {
            $year = Carbon::parse($input['year'])->format('Y');

            $bankReconciliation = $bankReconciliation->where('year', $year);
        }

        if (isset($input['bankAccountAutoID']) && $input['bankAccountAutoID'] > 0) {
            $bankReconciliation = $bankReconciliation->where('bankAccountAutoID', $input['bankAccountAutoID']);
        }

        if (isset($input['bankmasterAutoID']) && $input['bankmasterAutoID'] > 0) {
            $bankReconciliation = $bankReconciliation->whereHas('bank_account', function($query) use ($bankmasterAutoID) {
                                                            $query->whereIn('bankmasterAutoID', $bankmasterAutoID);
                                                    });
        }

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $bankReconciliation = $bankReconciliation->where(function ($query) use ($search) {
                $query->where('bankRecPrimaryCode', 'LIKE', "%{$search}%")
                    ->orWhereHas('ledger_data', function ($query) use ($search) {
                        $query->where('documentCode', 'LIKE', "%{$search}%");
                    });
            });
        }


        return $bankReconciliation;
    }

    public function setExportExcelData($dataSet) {

        $dataSet = $dataSet->get();
        if (count($dataSet) > 0) {
            $x = 0;

            foreach ($dataSet as $val) {
                $data[$x][trans('custom.brc_code')] = $val->bankRecPrimaryCode;
                $data[$x][trans('custom.month')] = $val->month? date('M', strtotime($val->bankRecAsOf)) : '';
                $data[$x][trans('custom.year')] = $val->year;
                $data[$x][trans('custom.bank_name')] = $val->bank_account? $val->bank_account->bankName : '';
                $data[$x][trans('custom.account_no')] = $val->bank_account? $val->bank_account->AccountNo : '';
                $data[$x][trans('custom.as_of')] = \Helper::dateFormat($val->bankRecAsOf);
                $data[$x][trans('custom.description')] = $val->description;
                $data[$x][trans('custom.created_by')] = $val->created_by? $val->created_by->empName : '';
                $data[$x][trans('custom.status')] = StatusService::getStatus($val->canceledYN, NULL, $val->confirmedYN, $val->approvedYN, $val->refferedBackYN);

                $x++;
            }
        } else {
            $data = array();
        }

        return $data;
    }
}
