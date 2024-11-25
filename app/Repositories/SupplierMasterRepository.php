<?php

namespace App\Repositories;

use App\helper\email;
use App\Models\Company;
use App\Models\SupplierMaster;
use InfyOm\Generator\Common\BaseRepository;


/**
 * Class SupplierMasterRepository
 * @package App\Repositories
 * @version February 21, 2018, 11:27 am UTC
 *
 * @method SupplierMaster findWithoutFail($id, $columns = ['*'])
 * @method SupplierMaster find($id, $columns = ['*'])
 * @method SupplierMaster first($columns = ['*'])
*/
class SupplierMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        //'uniqueTextcode',
        //'primaryCompanySystemID' => 'like',
        //'primaryCompanyID' => 'like',
        'primarySupplierCode' => 'like',
        //'secondarySupplierCode' => 'like',
        'supplierName' => 'like',
        /*'liabilityAccountSysemID',
        'liabilityAccount',
        'UnbilledGRVAccountSystemID',
        'UnbilledGRVAccount',
        'address',
        'countryID',
        'supplierCountryID',
        'telephone',
        'fax',
        'supEmail',
        'webAddress',
        'currency',
        'nameOnPaymentCheque',
        'creditLimit',
        'creditPeriod',
        'supCategoryMasterID',
        'supCategorySubID',
        'registrationNumber',
        'registrationExprity',
        'approvedby',
        'approvedYN',
        'approvedDate',
        'approvedComment',
        'isActive',
        'isSupplierForiegn',
        'supplierConfirmedYN',
        'supplierConfirmedEmpID',
        'supplierConfirmedEmpName',
        'supplierConfirmedDate',
        'isCriticalYN',
        'companyLinkedTo',
        'createdUserGroup',
        'createdPcID',
        'createdUserID',
        'modifiedPc',
        'modifiedUser',
        'createdDateTime',
        'isDirect',
        'supplierImportanceID',
        'supplierNatureID',
        'supplierTypeID',
        'WHTApplicable',
        'timestamp'*/
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SupplierMaster::class;
    }

    //supCategoryMasterID

    public function requestKycEnable($loginUrl, $name, $title, $tenderCode, $companyId, $email)
    {
        try {
            $company = Company::find($companyId);
            $companyName = $company->CompanyName ?? 'Company';
            $body = $this->generateKycEmailBody($name, $title, $tenderCode, $loginUrl, $companyName);
            $this->sendKycEmail($email, $companyId, $body, $title, $tenderCode);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function generateKycEmailBody($name, $title, $tenderCode, $loginUrl, $companyName)
    {
        return "Dear $name,<br /><br />
    We are pleased to inform you that, following the evaluation of the bids, your submission has been selected for the award of the $title - $tenderCode.<br /><br />
    To proceed with finalizing the award, please complete the required Know Your Customer (KYC) form at your earliest convenience. Kindly note that the tender will be officially awarded to you upon successful approval of your KYC details.<br /><br />
    Please access the KYC form through the link below and submit the necessary information to ensure the timely processing of your award: <br /><br />
    <b>Click Here:</b> <a href='$loginUrl'>$loginUrl</a><br /><br />
    If you have any questions or require assistance, please do not hesitate to contact us.<br /><br />
    Thank you for your prompt attention to this matter. We look forward to completing the award process and moving forward with the next steps.<br /><br />
    Thank You,<br />
    $companyName<br />";
    }

    private function sendKycEmail($email, $companyId, $body, $title, $tenderCode)
    {
        try {
            $dataEmail = [
                'companySystemID' => $companyId,
                'alertMessage' => "Notification of Award - $title - $tenderCode",
                'empEmail' => $email,
                'emailAlertMessage' => $body
            ];
            Email::sendEmailErp($dataEmail);
        } catch (\Exception $e) {
            throw new \Exception("Failed to send KYC email. Please check email configuration.");
        }
    }
}
