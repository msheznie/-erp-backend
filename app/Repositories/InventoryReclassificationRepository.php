<?php

namespace App\Repositories;

use App\Models\InventoryReclassification;
use InfyOm\Generator\Common\BaseRepository;
use App\helper\StatusService;

/**
 * Class InventoryReclassificationRepository
 * @package App\Repositories
 * @version August 10, 2018, 5:05 am UTC
 *
 * @method InventoryReclassification findWithoutFail($id, $columns = ['*'])
 * @method InventoryReclassification find($id, $columns = ['*'])
 * @method InventoryReclassification first($columns = ['*'])
*/
class InventoryReclassificationRepository extends BaseRepository
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
        'inventoryReclassificationDate',
        'narration',
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
        'RollLevForApp_curr',
        'rejectedYN',
        'timesReferred',
        'createdDateTime',
        'createdUserGroup',
        'createdPCid',
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
        return InventoryReclassification::class;
    }

    public function getAudit($id){
        return  $this->with(['created_by','confirmed_by','modified_by','company','details' => function($q){
            $q->with(['unit','itemmaster']);
        },'approved_by' => function ($query) {
            $query->with(['employee' =>  function($q){
                $q->with(['details.designation']);
            }])
                ->where('documentSystemID',61);
        },'audit_trial.modified_by'])
            ->findWithoutFail($id);
    }

    public function inventoryReclassificationListQuery($request, $input, $search = '') {

        $selectedCompanyId = $request['companyID'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $invReclassification = InventoryReclassification::with(['segment_by', 'created_by'])->whereIN('companySystemID', $subCompanies);

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $invReclassification = $invReclassification->where(function ($query) use ($search) {
                $query->where('documentCode', 'LIKE', "%{$search}%")
                    ->orWhere('narration', 'LIKE', "%{$search}%");
            });
        }

        return $invReclassification;
    }

    public function setExportExcelData($dataSet) {

        $dataSet = $dataSet->get();
        if (count($dataSet) > 0) {
            $x = 0;

            foreach ($dataSet as $val) {
                $data[$x][trans('custom.reclassification_code')] = $val->documentCode;
                $data[$x][trans('custom.segment')] = $val->segment_by? $val->segment_by->ServiceLineDes : '';
                $data[$x][trans('custom.reclassification_date')] = \Helper::dateFormat($val->inventoryReclassificationDate);
                $data[$x][trans('custom.comment')] = $val->narration;
                $data[$x][trans('custom.created_by')] = $val->created_by? $val->created_by->empName : '';
                $data[$x][trans('custom.created_at')] = \Helper::convertDateWithTime($val->createdDateTime);
                $data[$x][trans('custom.confirmed_at')] = \Helper::convertDateWithTime($val->confirmedDate);
                $data[$x][trans('custom.approved_at')] = \Helper::convertDateWithTime($val->approvedDate);
                $data[$x][trans('custom.status')] = StatusService::getStatus(NULL, NULL, $val->confirmedYN, $val->approved, $val->refferedBackYN);

                $x++;
            }
        } else {
            $data = array();
        }

        return $data;
    }
}
