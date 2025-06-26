<?php

namespace App\Services\AuditLog;

use App\helper\Helper;
use App\Models\AssetFinanceCategory;
use App\Models\AssetType;
use App\Models\ChartOfAccountsAssigned;
use App\Models\DepartmentMaster;
use App\Models\FinanceItemCategorySub;
use App\Models\FixedAssetCategory;
use App\Models\FixedAssetCategorySub;
use App\Models\ItemMaster;
use App\Models\Location;
use App\Models\SegmentMaster;
use App\Models\SupplierAssigned;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Support\Arr;
class AssetCostAuditService
{

    public static function process($auditData)
    {



        $modifiedData = [];
        if ($auditData['crudType'] == "U"){

            if($auditData['previosValue']['departmentSystemID'] != $auditData['newValue']['departmentSystemID']) {  
                $oldDepartment = DepartmentMaster::where('departmentSystemID',$auditData['previosValue']['departmentSystemID'] )->first();
                $newDepartment = DepartmentMaster::where('departmentSystemID',$auditData['newValue']['departmentSystemID'] )->first();
                
                $modifiedData[] = ['amended_field' => "department", 'previous_value' => ($oldDepartment) ? $oldDepartment->DepartmentDescription: '', 'new_value' => ($newDepartment) ? $newDepartment->DepartmentDescription : ''];
            }

            if($auditData['previosValue']['serviceLineSystemID'] != $auditData['newValue']['serviceLineSystemID']) {  
                $oldServiceLine = SegmentMaster::where('serviceLineSystemID',$auditData['previosValue']['serviceLineSystemID'] )->selectRaw('CONCAT(ServiceLineCode, " - " ,ServiceLineDes) as label')->first();
                $newServiceLine = SegmentMaster::where('serviceLineSystemID',$auditData['newValue']['serviceLineSystemID'] )->selectRaw('CONCAT(ServiceLineCode, " - " ,ServiceLineDes) as label')->first();
                
                $modifiedData[] = ['amended_field' => "service_line", 'previous_value' => ($oldServiceLine) ? $oldServiceLine->label: '', 'new_value' => ($newServiceLine) ? $newServiceLine->label : ''];
            }

            if($auditData['previosValue']['faUnitSerialNo'] != $auditData['newValue']['faUnitSerialNo']) {  
                $modifiedData[] = ['amended_field' => "serial_no", 'previous_value' => ($auditData['previosValue']['faUnitSerialNo']) ? $auditData['previosValue']['faUnitSerialNo']: '', 'new_value' => ($auditData['newValue']['faUnitSerialNo']) ? $auditData['newValue']['faUnitSerialNo'] : ''];
            }

            if($auditData['previosValue']['assetDescription'] != $auditData['newValue']['assetDescription']) {  
                $modifiedData[] = ['amended_field' => "description", 'previous_value' => ($auditData['previosValue']['assetDescription']) ? $auditData['previosValue']['assetDescription']: '', 'new_value' => ($auditData['newValue']['assetDescription']) ? $auditData['newValue']['assetDescription'] : ''];
            }

            if($auditData['previosValue']['MANUFACTURE'] != $auditData['newValue']['MANUFACTURE']) {  
                $modifiedData[] = ['amended_field' => "manufacture", 'previous_value' => ($auditData['previosValue']['MANUFACTURE']) ? $auditData['previosValue']['MANUFACTURE']: '', 'new_value' => ($auditData['newValue']['MANUFACTURE']) ? $auditData['newValue']['MANUFACTURE'] : ''];
            }

            if(isset($auditData['newValue']['dateAQ']) && ($auditData['previosValue']['dateAQ'] != $auditData['newValue']['dateAQ'])) {
                $newDateAaquired = $auditData['newValue']['dateAQ'];
                $carbonDateNew = Carbon::parse($newDateAaquired);
                $newDateAaquired = $carbonDateNew->toDateString();

                $previouDateAaquired = $auditData['previosValue']['dateAQ'];
                $carbonDatePrevious = Carbon::parse($previouDateAaquired);
                $previouDateAaquired = $carbonDatePrevious->toDateString();
                
                $modifiedData[] = ['amended_field' => "date_aquired", 'previous_value' => ($auditData['previosValue']['dateAQ']) ? $previouDateAaquired: '', 'new_value' => ($auditData['newValue']['dateAQ']) ? $newDateAaquired : ''];
            }

            if($auditData['previosValue']['faCatID'] != $auditData['newValue']['faCatID']) {  
                $oldFixedAssetCategory = FixedAssetCategory::where('faCatID',$auditData['previosValue']['faCatID'] )->first();
                $newFixedAssetCategory = FixedAssetCategory::where('faCatID',$auditData['newValue']['faCatID'] )->first();
                
                $modifiedData[] = ['amended_field' => "main_category", 'previous_value' => ($oldFixedAssetCategory) ? $oldFixedAssetCategory->catDescription: '', 'new_value' => ($newFixedAssetCategory) ? $newFixedAssetCategory->catDescription : ''];
            }

            if($auditData['previosValue']['faSubCatID'] != $auditData['newValue']['faSubCatID']) {  
                $oldFixedAssetCategorySub = FixedAssetCategorySub::where('faCatSubID',$auditData['previosValue']['faSubCatID'] )->first();
                $newFixedAssetCategorySub = FixedAssetCategorySub::where('faCatSubID',$auditData['newValue']['faSubCatID'] )->first();
                
                $modifiedData[] = ['amended_field' => "sub_category", 'previous_value' => ($oldFixedAssetCategorySub) ? $oldFixedAssetCategorySub->catDescription: '', 'new_value' => ($newFixedAssetCategorySub) ? $newFixedAssetCategorySub->catDescription : ''];
            }

            if($auditData['previosValue']['faSubCatID2'] != $auditData['newValue']['faSubCatID2']) {  
                $oldFixedAssetCategorySub2 = FixedAssetCategorySub::where('faCatSubID',$auditData['previosValue']['faSubCatID2'] )->first();
                $newFixedAssetCategorySub2 = FixedAssetCategorySub::where('faCatSubID',$auditData['newValue']['faSubCatID2'] )->first();
                
                $modifiedData[] = ['amended_field' => "sub_category2", 'previous_value' => ($oldFixedAssetCategorySub2) ? $oldFixedAssetCategorySub2->catDescription: '', 'new_value' => ($newFixedAssetCategorySub2) ? $newFixedAssetCategorySub2->catDescription : ''];
            }

            if($auditData['previosValue']['faSubCatID3'] != $auditData['newValue']['faSubCatID3']) {  
                $oldFixedAssetCategorySub3 = FixedAssetCategorySub::where('faCatSubID',$auditData['previosValue']['faSubCatID3'] )->first();
                $newFixedAssetCategorySub3 = FixedAssetCategorySub::where('faCatSubID',$auditData['newValue']['faSubCatID3'] )->first();
                
                $modifiedData[] = ['amended_field' => "sub_category3", 'previous_value' => ($oldFixedAssetCategorySub3) ? $oldFixedAssetCategorySub3->catDescription: '', 'new_value' => ($newFixedAssetCategorySub3) ? $newFixedAssetCategorySub3->catDescription : ''];
            }

            if($auditData['previosValue']['COMMENTS'] != $auditData['newValue']['COMMENTS']) {  
                $modifiedData[] = ['amended_field' => "comments", 'previous_value' => ($auditData['previosValue']['COMMENTS']) ? $auditData['previosValue']['COMMENTS']: '', 'new_value' => ($auditData['newValue']['COMMENTS']) ? $auditData['newValue']['COMMENTS'] : ''];
            }

            if($auditData['previosValue']['LOCATION'] != $auditData['newValue']['LOCATION']) {  
                $oldLocation = Location::where('locationID',$auditData['previosValue']['LOCATION'] )->first();
                $newLocation = Location::where('locationID',$auditData['newValue']['LOCATION'] )->first();
                
                $modifiedData[] = ['amended_field' => "location", 'previous_value' => ($oldLocation) ? $oldLocation->locationName: '', 'new_value' => ($newLocation) ? $newLocation->locationName : ''];
            }

            if(isset($auditData['newValue']['lastVerifiedDate']) && ($auditData['previosValue']['lastVerifiedDate'] != $auditData['newValue']['lastVerifiedDate'])) {
                $newLastVerifiedDate= $auditData['newValue']['lastVerifiedDate'];
                $carbonDateNew1 = Carbon::parse($newLastVerifiedDate);
                $newLastVerifiedDate = $carbonDateNew1->toDateString();

                $previousLastVerifiedDate = $auditData['previosValue']['lastVerifiedDate'];
                $carbonDatePrevious1 = Carbon::parse($previousLastVerifiedDate);
                $previousLastVerifiedDate = $carbonDatePrevious1->toDateString();
                
                $modifiedData[] = ['amended_field' => "last_physical_verified_date", 'previous_value' => ($auditData['previosValue']['lastVerifiedDate']) ? $previousLastVerifiedDate: '', 'new_value' => ($auditData['newValue']['lastVerifiedDate']) ? $newLastVerifiedDate : ''];
            }

            if(isset($auditData['newValue']['AUDITCATOGARY']) && ($auditData['previosValue']['AUDITCATOGARY'] != $auditData['newValue']['AUDITCATOGARY'])) {
                $oldAuditCategory = AssetFinanceCategory::where('faFinanceCatID',$auditData['previosValue']['AUDITCATOGARY'] )->first();
                $newAuditCategory = AssetFinanceCategory::where('faFinanceCatID',$auditData['newValue']['AUDITCATOGARY'] )->first();
                
                $modifiedData[] = ['amended_field' => "audit_category", 'previous_value' => ($oldAuditCategory) ? $oldAuditCategory->financeCatDescription: '', 'new_value' => ($newAuditCategory) ? $newAuditCategory->financeCatDescription : ''];
            }

            if(isset($auditData['newValue']['dateDEP']) && ($auditData['previosValue']['dateDEP'] != $auditData['newValue']['dateDEP'])) {
                $newDepStartDate= $auditData['newValue']['dateDEP'];
                $carbonDateNew2 = Carbon::parse($newDepStartDate);
                $newDepStartDate = $carbonDateNew2->toDateString();

                $previousDepStartDate = $auditData['previosValue']['dateDEP'];
                $carbonDatePrevious2 = Carbon::parse($previousDepStartDate);
                $previousDepStartDate = $carbonDatePrevious2->toDateString();
                
                $modifiedData[] = ['amended_field' => "dep_date_start", 'previous_value' => ($auditData['previosValue']['dateDEP']) ? $previousDepStartDate: '', 'new_value' => ($auditData['newValue']['dateDEP']) ? $newDepStartDate : ''];
            }

        }

        return $modifiedData;
    }

}
