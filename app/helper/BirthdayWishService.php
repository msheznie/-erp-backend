<?php

namespace App\helper;

use App\Models\SrpEmployeeDetails;
use Illuminate\Support\Facades\Log;

class BirthdayWishService
{

    public $company_id;
    public $company_code;
    public $company_name;

    public function __construct($company_data)
    {
        $this->company_id = $company_data['id'];
        $this->company_code = $company_data['code'];
        $this->company_name = $company_data['name'];

    }

    function execute()
    {
        $birthdayWishesPolicy = SME::policy($this->company_id, 'BRN', 'All');

        if($birthdayWishesPolicy == 1){
            $employeeDetails = $this->getEmployeeDetails($this->company_id);
            Log::useFiles( CommonJobService::get_specific_log_file('birthday-wishes') );
            if(!empty($employeeDetails)){
                $this->sendEachEmail($employeeDetails);
            }
        }
    }

    function getEmployeeDetails($companyId)
    {
        return $employeeDetails = SrpEmployeeDetails::select('Ename2', 'EEmail', 'EDOB','Erp_companyID')
        ->whereRaw('DATE_FORMAT(EDOB, "%m %d") = DATE_FORMAT(curdate() , "%m %d")')
        ->where('Erp_companyID', $companyId)
        ->where('isDischarged',0)
        ->get();
    }

    function sendEachEmail($employeeDetails)
    {
        foreach ($employeeDetails as $employee) {

            $emailData['empEmail'] = $employee->EEmail;
            $emailData['companySystemID'] = $employee->Erp_companyID;
            $temp = '<img src= '.public_path("image/Birthday-ASAAS-01.jpg").' /> ';
            $emailData['alertMessage'] = "Happy Birthday .$employee->Ename2.";
            $emailData['emailAlertMessage'] = $temp;
            $sendEmail = \Email::sendEmailErp($emailData);
            if(!$sendEmail["success"]){
                Log::useFiles( CommonJobService::get_specific_log_file('birthday-wishes') );
            }

        }
    }

}