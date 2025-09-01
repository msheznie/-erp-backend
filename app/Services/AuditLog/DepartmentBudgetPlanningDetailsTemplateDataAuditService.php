<?php

namespace App\Services\AuditLog;

use App\Models\ItemMaster;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;

class DepartmentBudgetPlanningDetailsTemplateDataAuditService
{
    public static function process($auditData)
    {
        $modifiedData = [];

        if ($auditData['crudType'] == "C") {
            // For creation, log all the new values

            $newValues = $auditData['newValue'];

            foreach ($newValues as $entryData) {
                if (isset($entryData['template_column']['pre_column']) && ($entryData['value'] != "")) {
                    if ($entryData['template_column']['pre_column']['columnType'] == 3) {
                        switch ($entryData['template_column']['pre_column']['preColumnID']) {
                            case 5:
                                $unit = Unit::where('UnitID', $entryData['value'])->first();
                                if ($unit) {
                                    $modifiedData[] = ['amended_field' => $entryData['template_column']['pre_column']['slug'], 'previous_value' => '', 'new_value' => $unit->UnitShortCode ?? ''];
                                }
                                break;
                            case 6:
                                $item = ItemMaster::where('itemCodeSystem', $entryData['value'])->first();
                                if ($item) {
                                    $modifiedData[] = ['amended_field' => $entryData['template_column']['pre_column']['slug'], 'previous_value' => '', 'new_value' => $item->itemDescription ?? ''];
                                }
                                break;
                            case 12:
                                $month = DB::table('erp_months')->where('monthsID', $entryData['value'])->first();
                                if ($month) {
                                    $modifiedData[] = ['amended_field' => $entryData['template_column']['pre_column']['slug'], 'previous_value' => '', 'new_value' => $month->monthDes ?? ''];
                                }
                                break;
                        }
                    }
                    else {
                        $modifiedData[] = ['amended_field' => $entryData['template_column']['pre_column']['slug'], 'previous_value' => '', 'new_value' => $entryData['value']];
                    }
                }
            }
        }
        else if ($auditData['crudType'] == "U") {
            // For updates, compare old and new values

            $oldValues = $auditData['previosValue'];
            $newValues = collect($auditData['newValue']);

            foreach ($oldValues as $entryData) {
                $newData = $newValues->where('templateColumnID', $entryData['templateColumnID'])->first();
                if (isset($entryData['template_column']['pre_column']) && (($entryData['value'] != "") || ($newData['value'] != ""))) {
                    if ($entryData['template_column']['pre_column']['columnType'] == 3) {
                        switch ($entryData['template_column']['pre_column']['preColumnID']) {
                            case 5:
                                if ($newData) {
                                    if($entryData['value'] != $newData['value']) {
                                        $oldUnit = Unit::where('UnitID', $entryData['value'])->first();
                                        $newUnit = Unit::where('UnitID', $newData['value'])->first();
                                        if ($oldUnit && $newUnit) {
                                            $modifiedData[] = ['amended_field' => $entryData['template_column']['pre_column']['slug'], 'previous_value' => $oldUnit->UnitShortCode ?? '', 'new_value' => $newUnit->UnitShortCode ?? ''];
                                        }
                                    }
                                }
                                break;
                            case 6:
                                if ($newData) {
                                    if($entryData['value'] != $newData['value']) {
                                        $oldItem = ItemMaster::where('itemCodeSystem', $entryData['value'])->first();
                                        $newItem = ItemMaster::where('itemCodeSystem', $newData['value'])->first();
                                        if ($oldItem && $newItem) {
                                            $modifiedData[] = ['amended_field' => $entryData['template_column']['pre_column']['slug'], 'previous_value' => $oldItem->itemDescription ?? '', 'new_value' => $newItem->itemDescription ?? ''];
                                        }
                                    }
                                }
                                break;
                            case 12:
                                if ($newData) {
                                    if ($entryData['value'] != $newData['value']) {
                                        $oldMonth = DB::table('erp_months')->where('monthsID', $entryData['value'])->first();
                                        $newMonth = DB::table('erp_months')->where('monthsID', $newData['value'])->first();
                                        if ($oldMonth && $newMonth) {
                                            $modifiedData[] = ['amended_field' => $entryData['template_column']['pre_column']['slug'], 'previous_value' => $oldMonth->monthDes ?? '', 'new_value' => $newMonth->monthDes ?? ''];
                                        }
                                    }
                                }
                                break;
                        }
                    }
                    else {
                        $modifiedData[] = ['amended_field' => $entryData['template_column']['pre_column']['slug'], 'previous_value' => $entryData['value'] ?? '', 'new_value' => $newData['value'] ?? ''];
                    }
                }
            }
        }
        else if ($auditData['crudType'] == "D") {
            // For deletion, log all the previous values

            $oldValues = $auditData['previosValue'];

            foreach ($oldValues as $oldValue) {
                if (isset($oldValue['template_column']['pre_column']) && ($oldValue['value'] != "")) {
                    if ($oldValue['template_column']['pre_column']['columnType'] == 3) {
                        switch ($oldValue['template_column']['pre_column']['preColumnID']) {
                            case 5:
                                $unit = Unit::where('UnitID', $oldValue['value'])->first();
                                if ($unit) {
                                    $modifiedData[] = ['amended_field' => $oldValue['template_column']['pre_column']['slug'], 'previous_value' => $unit->UnitShortCode ?? '', 'new_value' => ''];
                                }
                                break;
                            case 6:
                                $item = ItemMaster::where('itemCodeSystem', $oldValue['value'])->first();
                                if ($item) {
                                    $modifiedData[] = ['amended_field' => $oldValue['template_column']['pre_column']['slug'], 'previous_value' => $item->itemDescription ?? '', 'new_value' => ''];
                                }
                                break;
                            case 12:
                                $month = DB::table('erp_months')->where('monthsID', $oldValue['value'])->first();
                                if ($month) {
                                    $modifiedData[] = ['amended_field' => $oldValue['template_column']['pre_column']['slug'], 'previous_value' => $month->monthDes ?? '', 'new_value' => ''];
                                }
                                break;
                        }
                    }
                    else {
                        $modifiedData[] = ['amended_field' => $oldValue['template_column']['pre_column']['slug'], 'previous_value' => $oldValue['value'], 'new_value' => ''];
                    }
                }
            }
        }

        return $modifiedData;
    }
}
