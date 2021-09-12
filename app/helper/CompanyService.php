<?php

namespace App\helper;

use App\Models\Company;
use App\Models\CompanyDocumentAttachment;
use App\Models\CompanyPolicyCategory;
use App\Models\CompanyPolicyMaster;
use App\Models\DocumentMaster;

class CompanyService
{
    public static function hrIntegrated_company_count( $company_list ){
        return Company::selectRaw('COUNT(companySystemID), ')
            ->whereIn('companySystemID', $company_list)
            ->where('isHrmsIntergrated', 1)
            ->count();
    }

    public static function assign_policies( $company_id, $company_code ){
        $policies = self::unassigned_policies( $company_id );

        if(empty($policies)){
            return false;
        }

        $policies = $policies->toArray();

        $data = [];
        foreach ($policies as $policy){
            $data[] = [
                'documentID'=> $policy['documentID'],
                'companySystemID'=> $company_id,
                'companyID'=> $company_code,
                'companyPolicyCategoryID'=> $policy['companyPolicyCategoryID'],
                'isYesNO'=> 0,
            ];
        }

        CompanyPolicyMaster::insert( $data );

        return true;
    }

    public static function unassigned_policies( $company_id ){
        return CompanyPolicyCategory::selectRaw('companyPolicyCategoryID, documentID')
            ->whereDoesntHave('company_policy_master', function($q) use ($company_id){
                $q->where('companySystemID', $company_id);
            })
            ->orderBy('companyPolicyCategoryID')
            ->get();
    }

    public static function assign_document_attachments( $company_id, $company_code ){
        $doc_att = self::unassigned_document_attachments($company_id);

        if(empty($doc_att)){
            return false;
        }

        $doc_att = $doc_att->toArray();

        $data = [];
        foreach ($doc_att as $row){
            $data[] = [
                'documentSystemID'=> $row['documentSystemID'], 'documentID'=> $row['documentID'],
                'companySystemID'=> $company_id, 'companyID'=> $company_code, 'isAttachmentYN'=> 0,
                'sendEmailYN'=> 0, 'isAmountApproval'=> 0, 'isServiceLineApproval'=> 0,
                'isCategoryApproval'=> 0, 'blockYN'=> 0, 'enableAttachmentAfterApproval'=> 0
            ];
        }

        CompanyDocumentAttachment::insert( $data );

        return true;
    }

    public static function unassigned_document_attachments( $company_id ){
        return DocumentMaster::selectRaw('documentSystemID, documentID')
            ->whereDoesntHave('company_document_attachment', function($q) use ($company_id){
                $q->where('companySystemID', $company_id);
            })
            ->orderBy('documentSystemID')
            ->get();
    }

    public static function get_company_with_sub( $company_id ){
        $isGroup = Helper::checkIsCompanyGroup($company_id);

        if($isGroup){
            return  Helper::getGroupCompany($company_id);
        }

        return  [$company_id];
    }
}
