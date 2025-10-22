<?php

namespace App\Repositories;

use App\Models\StockTransfer;
use InfyOm\Generator\Common\BaseRepository;
use App\helper\StatusService;

/**
 * Class StockTransferRepository
 * @package App\Repositories
 * @version July 13, 2018, 5:27 am UTC
 *
 * @method StockTransfer findWithoutFail($id, $columns = ['*'])
 * @method StockTransfer find($id, $columns = ['*'])
 * @method StockTransfer first($columns = ['*'])
*/
class StockTransferRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companyID',
        'serviceLineCode',
        'companyFinanceYearID',
        'FYBiggin',
        'FYEnd',
        'documentID',
        'serialNo',
        'stockTransferCode',
        'refNo',
        'tranferDate',
        'comment',
        'companyFrom',
        'companyTo',
        'locationTo',
        'locationFrom',
        'confirmedYN',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approved',
        'postedDate',
        'fullyReceived',
        'timesReferred',
        'interCompanyTransferYN',
        'RollLevForApp_curr',
        'createdDateTime',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'modifiedUser',
        'modifiedPc',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return StockTransfer::class;
    }

    public function getAudit($id)
    {
        return $this->with(['created_by', 'confirmed_by', 'company', 'location_to_by', 'location_from_by', 'details' => function ($q) {
            $q->with(['unit_by','item_by']);
        }, 'modified_by', 'approved_by' => function ($query) {
            $query->with('employee.details.designation')
                ->where('documentSystemID', 13);
        },'audit_trial.modified_by'])
            ->findWithoutFail($id);
    }

    public function stockTransferListQuery($request, $input, $search = '', $grvLocation, $serviceLineSystemID) {

        $stockTransferMaster = StockTransfer::where('companySystemID', $input['companyId']);
        $stockTransferMaster->where('documentSystemID', $input['documentId']);
        $stockTransferMaster->with(['created_by' => function ($query) {
        }, 'segment_by' => function ($query) {
        }]);

        if (array_key_exists('serviceLineSystemID', $input)) {
            if ($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $stockTransferMaster->whereIn('serviceLineSystemID', $serviceLineSystemID);
            }
        }

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $stockTransferMaster->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $stockTransferMaster->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('interCompanyTransferYN', $input)) {
            if (($input['interCompanyTransferYN'] == 0 || $input['interCompanyTransferYN'] == -1) && !is_null($input['interCompanyTransferYN'])) {
                $stockTransferMaster->where('interCompanyTransferYN', $input['interCompanyTransferYN']);
            }
        }

        if (array_key_exists('locationFrom', $input)) {
            if ($input['locationFrom'] && !is_null($input['locationFrom'])) {
                $stockTransferMaster->whereIn('locationFrom', $grvLocation);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $stockTransferMaster->whereMonth('tranferDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $stockTransferMaster->whereYear('tranferDate', '=', $input['year']);
            }
        }

        $stockTransferMaster = $stockTransferMaster->select(
            ['erp_stocktransfer.stockTransferAutoID',
                'erp_stocktransfer.stockTransferCode',
                'erp_stocktransfer.documentSystemID',
                'erp_stocktransfer.refNo',
                'erp_stocktransfer.createdDateTime',
                'erp_stocktransfer.createdUserSystemID',
                'erp_stocktransfer.comment',
                'erp_stocktransfer.tranferDate',
                'erp_stocktransfer.serviceLineSystemID',
                'erp_stocktransfer.confirmedDate',
                'erp_stocktransfer.approvedDate',
                'erp_stocktransfer.timesReferred',
                'erp_stocktransfer.confirmedYN',
                'erp_stocktransfer.approved',
                'erp_stocktransfer.approvedDate',
                'erp_stocktransfer.fullyReceived',
                'erp_stocktransfer.refferedBackYN',
                'erp_stocktransfer.postedDate'
            ]);

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $stockTransferMaster = $stockTransferMaster->where(function ($query) use ($search) {
                $query->where('stockTransferCode', 'LIKE', "%{$search}%")
                    ->orWhere('comment', 'LIKE', "%{$search}%")
                    ->orWhere('refNo', 'LIKE', "%{$search}%");
            });
        }

        return $stockTransferMaster;
    }

    public function setExportExcelData($dataSet) {

        $dataSet = $dataSet->get();
        if (count($dataSet) > 0) {
            $x = 0;

            foreach ($dataSet as $val) {
                $data[$x][trans('custom.stock_transfer_code')] = $val->stockTransferCode;
                $data[$x][trans('custom.segment')] = $val->segment_by? $val->segment_by->ServiceLineDes : '';
                $data[$x][trans('custom.reference_no')] = $val->refNo;
                $data[$x][trans('custom.transfer_date')] = \Helper::dateFormat($val->tranferDate);
                $data[$x][trans('custom.comment')] = $val->comment;
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
