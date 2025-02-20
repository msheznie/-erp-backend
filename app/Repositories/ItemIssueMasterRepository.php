<?php

namespace App\Repositories;

use App\Models\ItemIssueDetails;
use App\Models\ItemIssueMaster;
use InfyOm\Generator\Common\BaseRepository;
use App\helper\StatusService;

/**
 * Class ItemIssueMasterRepository
 * @package App\Repositories
 * @version June 20, 2018, 4:23 am UTC
 *
 * @method ItemIssueMaster findWithoutFail($id, $columns = ['*'])
 * @method ItemIssueMaster find($id, $columns = ['*'])
 * @method ItemIssueMaster first($columns = ['*'])
 */
class ItemIssueMasterRepository extends BaseRepository
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
        'itemIssueCode',
        'issueType',
        'issueDate',
        'wareHouseFrom',
        'wareHouseFromCode',
        'wareHouseFromDes',
        'contractID',
        'jobNo',
        'workOrderNo',
        'purchaseOrderNo',
        'networkNo',
        'itemDeliveredOnSiteDate',
        'customerID',
        'issueRefNo',
        'reqDocID',
        'reqByID',
        'reqByName',
        'reqDate',
        'reqComment',
        'wellLocationFieldID',
        'fieldShortCode',
        'fieldName',
        'wellNO',
        'comment',
        'confirmedYN',
        'onfirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approved',
        'directReqByID',
        'directReqByName',
        'product',
        'volume',
        'strength',
        'createdDateTime',
        'createdUserGroup',
        'createdPCid',
        'createdUserSystemID',
        'createdUserID',
        'modifiedUserSystemID',
        'modifiedUser',
        'modifiedPc',
        'contRefNo',
        'is_closed',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ItemIssueMaster::class;
    }

    public function getAudit($id)
    {
        return $this->with(['created_by', 'confirmed_by', 'modified_by', 'warehouse_by', 'company', 'details' => function ($q1) {
              $q1->with(['uom_issuing', 'item_by', 'reportingCurreny' => function ($query) {
                  $query->select(['currencyID', 'CurrencyCode', 'DecimalPlaces']);
              }, 'allocate_employees' => function ($q2) {
                  $q2->with(['employee' => function ($q3) {
                      $q3->with(['hr_emp']);
                  }]);
              }]);
        }, 'approved_by' => function ($query) {
            $query->with(['employee' => function ($q) {
                $q->with(['details.designation']);
            }])->where('documentSystemID', 8);
        },'audit_trial.modified_by'])
            ->findWithoutFail($id);
    }

    public function itemIssueListQuery($request, $input, $search = '', $grvLocation, $serviceLineSystemID) {

        $selectedCompanyId = $request['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $itemIssueMaster = ItemIssueMaster::whereIn('companySystemID', $subCompanies)
            ->with(['created_by', 'warehouse_by', 'segment_by', 'customer_by','details']);


        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $itemIssueMaster->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $itemIssueMaster->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('serviceLineSystemID', $input)) {
            if ($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $itemIssueMaster->whereIn('serviceLineSystemID', $serviceLineSystemID);
            }
        }

        if (array_key_exists('wareHouseFrom', $input)) {
            if ($input['wareHouseFrom'] && !is_null($input['wareHouseFrom'])) {
                $itemIssueMaster->whereIn('wareHouseFrom', $grvLocation);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $itemIssueMaster->whereMonth('issueDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $itemIssueMaster->whereYear('issueDate', '=', $input['year']);
            }
        }


        $itemIssueMaster = $itemIssueMaster->select(
            ['erp_itemissuemaster.itemIssueAutoID',
                'erp_itemissuemaster.itemIssueCode',
                'erp_itemissuemaster.comment',
                'erp_itemissuemaster.issueDate',
                'erp_itemissuemaster.customerSystemID',
                'erp_itemissuemaster.confirmedYN',
                'erp_itemissuemaster.approved',
                'erp_itemissuemaster.serviceLineSystemID',
                'erp_itemissuemaster.documentSystemID',
                'erp_itemissuemaster.confirmedByEmpSystemID',
                'erp_itemissuemaster.createdUserSystemID',
                'erp_itemissuemaster.confirmedDate',
                'erp_itemissuemaster.approvedDate',
                'erp_itemissuemaster.createdDateTime',
                'erp_itemissuemaster.issueRefNo',
                'erp_itemissuemaster.wareHouseFrom',
                'erp_itemissuemaster.refferedBackYN',
                'erp_itemissuemaster.issueType',
                'erp_itemissuemaster.postedDate'
            ]);

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $itemIssueMaster = $itemIssueMaster->where(function ($query) use ($search) {
                $query->where('itemIssueCode', 'LIKE', "%{$search}%")
                    ->orWhere('comment', 'LIKE', "%{$search}%")
                    ->orWhere('issueRefNo', 'LIKE', "%{$search}%");
            });
        }


        return $itemIssueMaster;
    }

    public function setExportExcelData($dataSet) {

        $dataSet = $dataSet->get();
        if (count($dataSet) > 0) {
            $x = 0;

            foreach ($dataSet as $val) {
                $data[$x]['Issue Code'] = $val->itemIssueCode;
                $data[$x]['Segment'] = $val->segment_by? $val->segment_by->ServiceLineDes : '';
                $data[$x]['Reference No'] = $val->issueRefNo;
                $data[$x]['Issue Date'] = \Helper::dateFormat($val->issueDate);
                $data[$x]['Location'] = $val->warehouse_by? $val->warehouse_by->wareHouseDescription : '';
                $data[$x]['Comment'] = $val->comment;
                $data[$x]['Created By'] = $val->created_by? $val->created_by->empName : '';
                $data[$x]['Created At'] = \Helper::convertDateWithTime($val->createdDateTime);
                $data[$x]['Confirmed at'] = \Helper::convertDateWithTime($val->confirmedDate);
                $data[$x]['Approved at'] = \Helper::convertDateWithTime($val->approvedDate);
                $data[$x]['Status'] = StatusService::getStatus($val->CancelledYN, NULL, $val->confirmedYN, $val->approved, $val->refferedBackYN);

                $x++;
            }
        } else {
            $data = array();
        }

        return $data;
    }
}
