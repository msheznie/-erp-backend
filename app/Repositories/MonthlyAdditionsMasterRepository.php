<?php

namespace App\Repositories;

use App\Models\MonthlyAdditionsMaster;
use InfyOm\Generator\Common\BaseRepository;
use App\helper\StatusService;

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

    public function monthlyAdditionsListQuery($request, $input, $search = '') {

        $selectedCompanyId = $request['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $monthlyAdditions = MonthlyAdditionsMaster::whereIn('companySystemID', $subCompanies)
            ->with('currency_by')
            ->where('expenseClaimAdditionYN', 1)
            ->where('documentSystemID', $input['documentId']);

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $monthlyAdditions = $monthlyAdditions->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $monthlyAdditions = $monthlyAdditions->where('approvedYN', $input['approved']);
            }
        }


        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $monthlyAdditions = $monthlyAdditions->where(function ($query) use ($search) {
                $query->where('monthlyAdditionsCode', 'LIKE', "%{$search}%");
            });
        }

        return $monthlyAdditions;
    }

    public function setExportExcelData($dataSet) {

        $dataSet = $dataSet->get();
        if (count($dataSet) > 0) {
            $x = 0;

            foreach ($dataSet as $val) {
                $data[$x][__('custom.date')] = \Helper::dateFormat($val->dateMA);
                $data[$x][__('custom.document_code')] = $val->monthlyAdditionsCode;
                $data[$x][__('custom.description')] = $val->description;
                $data[$x][__('custom.currency')] = $val->currency_by? $val->currency_by->CurrencyCode : '';
                $data[$x][__('custom.status')] = StatusService::getStatus($val->canceledYN, NULL, $val->confirmedYN, $val->approvedYN, $val->timesReferred);

                $x++;
            }
        } else {
            $data = array();
        }

        return $data;
    }
}
