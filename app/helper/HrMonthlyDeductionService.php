<?php


namespace App\helper;


use App\Models\HrMonthlyDeductionMaster;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\SrpEmployeeDetails;

class HrMonthlyDeductionService
{
    private $pv_id;
    private $pv_master;
    private $pv_details = [];
    private $current_user = [];

    public function __construct($id)
    {
        $this->pv_id = $id;
    }

    public function create_monthly_deduction(){
        $this->pv_master = PaySupplierInvoiceMaster::find($this->pv_id);

        if( empty($this->pv_master) ){
            throw new \Exception("Payment voucher master data not found", 404);
        }

        if( empty($this->pv_master->createMonthlyDeduction) ){
            return true;
        }

        $this->create_header();

        return true;
    }

    function set_user_details(){
        $emp_id = Helper::getEmployeeSystemID();

        $emp_det = SrpEmployeeDetails::find($emp_id);

        $this->current_user = [
            'user_id' => $emp_id,
        ];
    }

    function create_header(){
        $this->set_user_details();
        //

        $header = new HrMonthlyDeductionMaster;

        //$header->fillable
        $header->monthlyDeductionCode = null;
        $header->serialNo = null;
        $header->documentID = null;
        $header->description = null;
        $header->currencyID = null;
        $header->currency = null;
        $header->dateMD = null;
        $header->isNonPayroll = null;

        $header->confirmedYN = 1;
        $header->confirmedByEmpID = null;
        $header->confirmedByName = null;
        $header->confirmedDate = null;

        $header->companyID = null;
        $header->companyCode = null;

        $header->createdUserGroup = null;
        $header->createdPCID = null;
        $header->createdUserID = null;
        $header->createdUserName = null;
    }
}
