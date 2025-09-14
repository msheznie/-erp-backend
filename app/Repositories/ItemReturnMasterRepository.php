<?php

namespace App\Repositories;

use App\Models\ItemReturnMaster;
use InfyOm\Generator\Common\BaseRepository;
use App\helper\StatusService;

/**
 * Class ItemReturnMasterRepository
 * @package App\Repositories
 * @version July 16, 2018, 4:53 am UTC
 *
 * @method ItemReturnMaster findWithoutFail($id, $columns = ['*'])
 * @method ItemReturnMaster find($id, $columns = ['*'])
 * @method ItemReturnMaster first($columns = ['*'])
 */
class ItemReturnMasterRepository extends BaseRepository
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
        'itemReturnCode',
        'ReturnType',
        'ReturnDate',
        'ReturnedBy',
        'jobNo',
        'customerID',
        'wareHouseLocation',
        'ReturnRefNo',
        'comment',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approved',
        'approvedDate',
        'postedDate',
        'RollLevForApp_curr',
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
        return ItemReturnMaster::class;
    }

    public function getAudit($id)
    {

        return $this->with(['created_by', 'confirmed_by', 'modified_by', 'warehouse_by', 'company', 'details' => function ($q) {
            $q->with(['uom_issued', 'uom_receiving','item_by']);
        }, 'approved_by' => function ($query) {
            $query->with('employee.details.designation')
                ->where('documentSystemID', 12);
        },'audit_trial.modified_by'])
            ->findWithoutFail($id);
    }

    public function itemReturnListQuery($request, $input, $search = '', $grvLocation, $serviceLineSystemID) {

        $selectedCompanyId = $request['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $itemReturnMaster = ItemReturnMaster::whereIn('companySystemID', $subCompanies)
            ->with(['created_by', 'warehouse_by', 'segment_by', 'customer_by']);


        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $itemReturnMaster->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $itemReturnMaster->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('serviceLineSystemID', $input)) {
            if ($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $itemReturnMaster->whereIn('serviceLineSystemID', $serviceLineSystemID);
            }
        }

        if (array_key_exists('wareHouseLocation', $input)) {
            if ($input['wareHouseLocation'] && !is_null($input['wareHouseLocation'])) {
                $itemReturnMaster->whereIn('wareHouseLocation', $grvLocation);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $itemReturnMaster->whereMonth('ReturnDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $itemReturnMaster->whereYear('ReturnDate', '=', $input['year']);
            }
        }


        $itemReturnMaster = $itemReturnMaster->select(
            ['itemReturnAutoID',
                'itemReturnCode',
                'comment',
                'ReturnDate',
                'confirmedYN',
                'approved',
                'serviceLineSystemID',
                'documentSystemID',
                'confirmedByEmpSystemID',
                'createdUserSystemID',
                'confirmedDate',
                'approvedDate',
                'createdDateTime',
                'ReturnRefNo',
                'wareHouseLocation',
                'refferedBackYN'
            ]);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $itemReturnMaster = $itemReturnMaster->where(function ($query) use ($search) {
                $query->where('itemReturnCode', 'LIKE', "%{$search}%")
                    ->orWhere('comment', 'LIKE', "%{$search}%");
            });
        }

        return $itemReturnMaster;
    }

    public function setExportExcelData($dataSet) {

        $dataSet = $dataSet->get();
        if (count($dataSet) > 0) {
            $x = 0;

            foreach ($dataSet as $val) {
                $data[$x][trans('custom.e_item_return_code')] = $val->itemReturnCode;
                $data[$x][trans('custom.e_department')] = $val->segment_by? $val->segment_by->ServiceLineDes : '';
                $data[$x][trans('custom.e_reference_no')] = $val->ReturnRefNo;
                $data[$x][trans('custom.e_return_date')] = \Helper::dateFormat($val->ReturnDate);
                $data[$x][trans('custom.e_warehouse')] = $val->warehouse_by? $val->warehouse_by->wareHouseDescription : '';
                $data[$x][trans('custom.e_comment')] = $val->comment;
                $data[$x][trans('custom.e_created_by')] = $val->created_by? $val->created_by->empName : '';
                $data[$x][trans('custom.e_created_at')] = \Helper::convertDateWithTime($val->createdDateTime);
                $data[$x][trans('custom.e_confirmed_at')] = \Helper::convertDateWithTime($val->confirmedDate);
                $data[$x][trans('custom.e_approved_at')] = \Helper::convertDateWithTime($val->approvedDate);
                $data[$x][trans('custom.e_status')] = StatusService::getStatus($val->CancelledYN, NULL, $val->confirmedYN, $val->approved, $val->refferedBackYN);

                $x++;
            }
        } else {
            $data = array();
        }

        return $data;
    }
}
