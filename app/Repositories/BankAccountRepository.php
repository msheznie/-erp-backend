<?php

namespace App\Repositories;

use App\Models\BankAccount;
use InfyOm\Generator\Common\BaseRepository;
use App\helper\StatusService;

/**
 * Class BankAccountRepository
 * @package App\Repositories
 * @version March 30, 2018, 9:40 am UTC
 *
 * @method BankAccount findWithoutFail($id, $columns = ['*'])
 * @method BankAccount find($id, $columns = ['*'])
 * @method BankAccount first($columns = ['*'])
*/
class BankAccountRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'bankAssignedAutoID',
        'bankmasterAutoID',
        'companyID',
        'bankShortCode',
        'bankName',
        'bankBranch',
        'BranchCode',
        'BranchAddress',
        'BranchContactPerson',
        'BranchTel',
        'BranchFax',
        'BranchEmail',
        'AccountNo',
        'AccountName',
        'accountCurrencyID',
        'accountSwiftCode',
        'accountIBAN#',
        'chqueManualStartingNo',
        'isManualActive',
        'chquePrintedStartingNo',
        'isPrintedActive',
        'glCodeLinked',
        'extraNote',
        'isAccountActive',
        'isDefault',
        'approvedYN',
        'approvedByEmpID',
        'approvedEmpName',
        'approvedDate',
        'approvedComments',
        'createdDateTime',
        'createdEmpID',
        'createdPCID',
        'modifedDateTime',
        'modifiedByEmpID',
        'modifiedPCID',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return BankAccount::class;
    }

    public function getAudit($id)
    {
        return $this->with(['created_by', 'confirmed_by', 'modified_by', 'company', 'approved_by' => function ($query) {
            $query->with(['employee' => function ($q) {
                $q->with(['details.designation']);
            }])->where('documentSystemID', 66);
        }])->findWithoutFail($id);
    }

    public function bankAccountListQuery($request, $input, $search = '', $bankmasterAutoID) {

        $selectedCompanyId = $request['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $logistics = BankAccount::whereIn('companySystemID', $subCompanies)
            ->when(request('bankmasterAutoID',false), function ($q) use ($bankmasterAutoID) {
                $q->whereIn('bankmasterAutoID', $bankmasterAutoID);
            })
            ->with(['currency']);

        if (array_key_exists('isAccountActive', $input)) {
            if (($input['isAccountActive'] == 0 || $input['isAccountActive'] == 1) && !is_null($input['isAccountActive'])) {
                $logistics->where('isAccountActive', $input['isAccountActive']);
            }
        }

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $logistics = $logistics->where(function ($query) use ($search) {
                $query->where('bankShortCode', 'LIKE', "%{$search}%")
                    ->orWhere('bankName', 'LIKE', "%{$search}%");
            });
        }

        return $logistics;
    }

    public function setExportExcelData($dataSet) {

        $dataSet = $dataSet->get();
        if (count($dataSet) > 0) {
            $x = 0;

            foreach ($dataSet as $val) {
                $data[$x][trans('custom.short_code')] = $val->bankShortCode;
                $data[$x][trans('custom.bank_name')] = $val->bankName;
                $data[$x][trans('custom.currency')] = $val->currency? $val->currency->CurrencyCode : '';
                $data[$x][trans('custom.account_no')] = $val->AccountNo;
                $data[$x][trans('custom.gl_code')] = $val->glCodeLinked;
                $data[$x][trans('custom.bank_branch')] = $val->bankBranch;
                $data[$x][trans('custom.swift_code')] = $val->accountSwiftCode;
                $data[$x][trans('custom.bank_balance')] = $val->amounts? number_format($val->amounts->bankBalance, $val->currency? $val->currency->DecimalPlaces : 2, ".", "") : 0;
                $data[$x][trans('custom.with_treasury_amount')] = $val->amounts? number_format($val->amounts->withTreasury, $val->currency? $val->currency->DecimalPlaces : 2, ".", "") : 0;
                $data[$x][trans('custom.net_bank_balance')] = $val->amounts? number_format($val->amounts->netBankBalance, $val->currency? $val->currency->DecimalPlaces : 2, ".", "") : 0;
                $data[$x][trans('custom.status')] = $val->isAccountActive == 1? trans('custom.active') : trans('custom.not_active');

                $x++;
            }
        } else {
            $data = array();
        }

        return $data;
    }

}
