<?php

namespace App\Services\AuditLog;

use App\helper\Helper;
use App\Models\FinanceItemCategorySub;
use App\Models\ItemMaster;
use App\Models\Unit;
use Illuminate\Support\Arr;
class ItemMasterAuditService
{

    public static function process($auditData)
    {



        $modifiedData = [];
        if ($auditData['crudType'] == "U"){

                if($auditData['previosValue']['itemUrl'] != $auditData['newValue']['itemUrl']) {  
                    $modifiedData[] = ['amended_field' => "item_url", 'previous_value' => ($auditData['previosValue']['itemUrl']) ? $auditData['previosValue']['itemUrl']: '', 'new_value' => ($auditData['newValue']['itemUrl']) ? $auditData['newValue']['itemUrl'] : ''];
                }
                
                if($auditData['previosValue']['isActive'] != $auditData['newValue']['isActive']) {  
                    $modifiedData[] = ['amended_field' => "is_active", 'previous_value' => ($auditData['previosValue']['isActive'] == 1) ? 'Yes': 'No', 'new_value' => ($auditData['newValue']['isActive'] == 1) ? 'Yes': 'No'];
                }

                if($auditData['previosValue']['isSubItem'] != $auditData['newValue']['isSubItem']) {  
                    $modifiedData[] = ['amended_field' => "is_subitem", 'previous_value' => ($auditData['previosValue']['isSubItem'] == 1) ? 'Yes': 'No', 'new_value' => ($auditData['newValue']['isSubItem'] == 1) ? 'Yes': 'No'];
                }

                if($auditData['previosValue']['mainItemID'] != $auditData['newValue']['mainItemID']) {  
                    $previousMainItem = ItemMaster::where('itemCodeSystem',$auditData['previosValue']['mainItemID'])->first();
                    $newMainItem = ItemMaster::where('itemCodeSystem',$auditData['newValue']['mainItemID'])->first();
                    $modifiedData[] = ['amended_field' => "main_item", 'previous_value' => ($previousMainItem) ? $previousMainItem->itemDescription: '', 'new_value' => ($newMainItem) ? $newMainItem->itemDescription : ''];
                }

                if($auditData['previosValue']['pos_type'] != $auditData['newValue']['pos_type']) {  
                    $previousPosType = '';
                    $newPosType = '';
                    if($auditData['previosValue']['pos_type'] == 1){
                        $previousPosType = 'General POS';
                    } elseif ($auditData['previosValue']['pos_type'] == 2){
                        $previousPosType = 'Restaurant POS';
                    } elseif ($auditData['previosValue']['pos_type'] == 3){
                        $previousPosType = 'Both';
                    }

                    if($auditData['newValue']['pos_type'] == 1){
                        $newPosType = 'General POS';
                    } elseif ($auditData['newValue']['pos_type'] == 2){
                        $newPosType = 'Restaurant POS';
                    } elseif ($auditData['newValue']['pos_type'] == 3){
                        $newPosType = 'Both POS';
                    }

                    $modifiedData[] = ['amended_field' => "pos_type", 'previous_value' => ($previousPosType) ? $previousPosType: '', 'new_value' => ($newPosType) ? $newPosType : ''];
                }

                if($auditData['previosValue']['itemDescription'] != $auditData['newValue']['itemDescription']) {  
                    $modifiedData[] = ['amended_field' => "item_description", 'previous_value' => ($auditData['previosValue']['itemDescription']) ? $auditData['previosValue']['itemDescription']: '', 'new_value' => ($auditData['newValue']['itemDescription']) ? $auditData['newValue']['itemDescription'] : ''];
                }

                if($auditData['previosValue']['itemShortDescription'] != $auditData['newValue']['itemShortDescription']) {  
                    $modifiedData[] = ['amended_field' => "item_short_description", 'previous_value' => ($auditData['previosValue']['itemShortDescription']) ? $auditData['previosValue']['itemShortDescription']: '', 'new_value' => ($auditData['newValue']['itemShortDescription']) ? $auditData['newValue']['itemShortDescription'] : ''];
                }

                if($auditData['previosValue']['financeCategorySub'] != $auditData['newValue']['financeCategorySub']) { 
                    $previousFinanceCategorySub =FinanceItemCategorySub::where('itemCategorySubID',$auditData['previosValue']['financeCategorySub'])->first();
                    $newFinanceCategorySub =FinanceItemCategorySub::where('itemCategorySubID',$auditData['newValue']['financeCategorySub'])->first();
                    $modifiedData[] = ['amended_field' => "finance_category_sub", 'previous_value' => ($previousFinanceCategorySub) ? $previousFinanceCategorySub->categoryDescription: '', 'new_value' => ($newFinanceCategorySub) ? $newFinanceCategorySub->categoryDescription: ''];
                }

                if($auditData['previosValue']['unit'] != $auditData['newValue']['unit']) {  
                    $previosUnit = Unit::where('UnitID',$auditData['previosValue']['unit'] )->first();
                    $newUnit = Unit::where('UnitID',$auditData['newValue']['unit'] )->first();
                    $modifiedData[] = ['amended_field' => "unit_of_measure", 'previous_value' => ($previosUnit) ? $previosUnit->UnitShortCode: '', 'new_value' => ($newUnit) ? $newUnit->UnitShortCode : ''];
                }

                if($auditData['previosValue']['barcode'] != $auditData['newValue']['barcode']) {  
                    $modifiedData[] = ['amended_field' => "barcode", 'previous_value' => ($auditData['previosValue']['barcode']) ? $auditData['previosValue']['barcode']: '', 'new_value' => ($auditData['newValue']['barcode']) ? $auditData['newValue']['barcode'] : ''];
                }

                if($auditData['previosValue']['secondaryItemCode'] != $auditData['newValue']['secondaryItemCode']) {  
                    $modifiedData[] = ['amended_field' => "mfg_part_no", 'previous_value' => ($auditData['previosValue']['secondaryItemCode']) ? $auditData['previosValue']['secondaryItemCode']: '', 'new_value' => ($auditData['newValue']['secondaryItemCode']) ? $auditData['newValue']['secondaryItemCode'] : ''];
                }
        }

        return $modifiedData;
    }

}
