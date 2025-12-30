<?php

namespace App\Services\AuditLog;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AuditLogCommonService
{
    /**
     * Translate narration with dynamic variables
     * Uses switch-case for tables and if-else for crudType
     * Directly uses trans() function with translation keys
     * 
     * @param string $narrationVariables Dynamic variables only (empty string if static)
     * @param string $table Table name
     * @param string $crudType C, U, or D
     * @param string $locale Language code (e.g., 'en', 'ar')
     * @param string $parentTable Optional parent table for disambiguation
     * @return string Translated narration with variables injected at exact positions
     */
    public static function translateNarration($narrationVariables, $table, $crudType, $locale, $parentTable = null)
    {
        try {
            $translationKey = self::getTranslationKey($table, $crudType, $parentTable);
            
            $translated = trans($translationKey, [], $locale);
            
            if ($translated === $translationKey) {
                Log::warning("Translation key not found: {$translationKey} for locale: {$locale}");
                $translated = trans($translationKey, [], 'en');
                if ($translated === $translationKey) {
                    $translated = '';
                }
            }
            
            if (!empty($narrationVariables)) {
                $translated = str_replace('{variable}', $narrationVariables, $translated);
            }
            
            return $translated;
        } catch (\Exception $e) {
            Log::error('AuditLogTranslationService::translateNarration failed: ' . $e->getMessage());
            
            if (!empty($narrationVariables)) {
                return $narrationVariables;
            }
            return '';
        }
    }
    
    /**
     * Get translation key for table + crudType
     * Uses switch-case for tables and if-else for crudType
     * 
     * @param string $table
     * @param string $crudType
     * @param string|null $parentTable
     * @return string Translation key
     */
    private static function getTranslationKey($table, $crudType, $parentTable = null)
    {
        switch ($table) {
            case 'company_departments':
                if ($crudType === 'C') {
                    return 'audit.department_master_variable_has_been_created';
                } elseif ($crudType === 'U') {
                    return 'audit.department_master_variable_has_been_updated';
                } elseif ($crudType === 'D') {
                    return 'audit.department_master_variable_has_been_deleted';
                }
                break;
                
            case 'company_departments_employees':
                if ($crudType === 'C') {
                    return 'audit.employee_assigned_to_department';
                } elseif ($crudType === 'U') {
                    return 'audit.department_employee_assignment_updated';
                } elseif ($crudType === 'D') {
                    return 'audit.employee_removed_from_department';
                }
                break;
                
            case 'company_departments_segments':
                if ($crudType === 'C') {
                    return 'audit.segment_assigned_to_department';
                } elseif ($crudType === 'U') {
                    return 'audit.department_segment_assignment_updated';
                } elseif ($crudType === 'D') {
                    return 'audit.segment_removed_from_department';
                }
                break;
                
            case 'department_budget_plannings':
                if ($crudType === 'C') {
                    return 'audit.department_budget_planning_variable_has_been_created';
                } elseif ($crudType === 'U') {
                    return 'audit.department_budget_planning_variable_has_been_updated';
                } elseif ($crudType === 'D') {
                    return 'audit.time_extension_request_variable_has_been_deleted';
                }
                break;
                
            case 'department_budget_planning_details_template_data':
                if ($crudType === 'C') {
                    return 'audit.budget_planning_detail_record_has_been_created';
                } elseif ($crudType === 'U') {
                    return 'audit.budget_planning_detail_record_has_been_updated';
                } elseif ($crudType === 'D') {
                    return 'audit.budget_planning_detail_record_has_been_deleted';
                }
                break;
                
            case 'suppliermaster':
                if ($crudType === 'U') {
                    return 'audit.variable_has_updated';
                }
                break;
                
            case 'serviceline':
                if ($crudType === 'U') {
                    return 'audit.segment_master_variable_has_been_updated';
                } elseif ($crudType === 'D') {
                    return 'audit.segment_master_variable_has_been_deleted';
                }
                break;
                
            case 'itemmaster':
                if ($crudType === 'U') {
                    return 'audit.variable_has_updated';
                }
                break;
                
            case 'chartofaccounts':
                if ($crudType === 'U') {
                    return 'audit.variable_has_updated';
                }
                break;
                
            case 'chart_of_account_config':
                if ($crudType === 'U') {
                    return 'audit.variable_has_updated';
                }
                break;
                
            case 'erp_attributes':
                if ($crudType === 'C') {
                    if ($parentTable === 'erp_fa_asset_master') {
                        return 'audit.attribute_dropdown_value_variable_has_been_created';
                    }
                    return 'audit.attribute_variable_has_created';
                } elseif ($crudType === 'U') {
                    if ($parentTable === 'erp_fa_asset_master') {
                        // Check if it's dropdown value update (handled by specific context)
                        return 'audit.attribute_variable_has_been_updated';
                    }
                    return 'audit.attribute_variable_has_been_updated';
                } elseif ($crudType === 'D') {
                    if ($parentTable === 'financeitemcategorysub') {
                        return 'audit.attribute_variable_has_deleted';
                    } elseif ($parentTable === 'erp_fa_asset_master') {
                        return 'audit.attribute_variable_has_been_deleted';
                    }
                    return 'audit.attribute_variable_has_been_deleted';
                }
                break;
                
            case 'erp_workflow_configurations':
                if ($crudType === 'C') {
                    return 'audit.workflow_configuration_variable_has_been_created';
                } elseif ($crudType === 'U') {
                    return 'audit.workflow_configuration_variable_has_been_updated';
                } elseif ($crudType === 'D') {
                    return 'audit.workflow_configuration_variable_has_been_deleted';
                }
                break;
                
            case 'erp_workflow_configuration_hod_actions':
                if ($crudType === 'C') {
                    return 'audit.hod_action_has_been_added_to_workflow_configuration';
                } elseif ($crudType === 'D') {
                    return 'audit.hod_action_has_been_deleted_during_workflow_update';
                }
                break;
                
            case 'erp_fa_asset_master':
                if ($crudType === 'U') {
                    return 'audit.variable_has_updated';
                }
                break;
                
            case 'erp_fa_financecategory':
                if ($crudType === 'U') {
                    return 'audit.asset_finance_category_has_updated';
                }
                break;
                
            case 'financeitemcategorysub':
                if ($crudType === 'C') {
                    return 'audit.variable_has_created';
                } elseif ($crudType === 'U') {
                    return 'audit.variable_has_updated';
                }
                break;
                
            case 'financeitemcategorysubassigned':
                if ($crudType === 'C') {
                    return 'audit.company_assign_variable_has_been_created';
                } elseif ($crudType === 'U') {
                    return 'audit.company_assign_variable_has_been_updated';
                } elseif ($crudType === 'D') {
                    return 'audit.company_assign_variable_has_been_deleted';
                }
                break;
                
            case 'budget_templates':
                if ($crudType === 'C') {
                    return 'audit.budget_template_variable_has_been_created';
                } elseif ($crudType === 'U') {
                    return 'audit.budget_template_variable_has_been_updated';
                } elseif ($crudType === 'D') {
                    return 'audit.budget_template_variable_has_been_deleted';
                }
                break;
                
            case 'department_budget_templates':
                if ($crudType === 'C') {
                    return 'audit.budget_template_assigned_to_department';
                } elseif ($crudType === 'U') {
                    return 'audit.department_budget_template_updated';
                } elseif ($crudType === 'D') {
                    return 'audit.department_budget_template_deleted';
                }
                break;
                
            case 'budget_template_columns':
                if ($crudType === 'C') {
                    return 'audit.budget_template_column_added';
                } elseif ($crudType === 'U') {
                    return 'audit.budget_template_column_updated';
                } elseif ($crudType === 'D') {
                    return 'audit.budget_template_column_deleted';
                }
                break;
                
            case 'department_user_budget_control':
                if ($crudType === 'U') {
                    return 'audit.budget_controls_updated_for_user';
                }
                break;
                
            case 'customermaster':
                if ($crudType === 'U') {
                    return 'audit.variable_has_updated';
                }
                break;

            case 'user_group':
                if ($crudType === 'C') {
                    return 'audit.user_group_has_been_created';
                } elseif ($crudType === 'U') {
                    return 'audit.user_group_has_updated';
                } elseif ($crudType === 'D') {
                    return 'audit.user_group_has_been_deleted';
                }
                break;

            case 'employee_navigation_assign':
                if ($crudType === 'C') {
                    return 'audit.employee_has_been_assigned_to_user_group';
                } elseif ($crudType === 'D') {
                    return 'audit.employee_has_been_unassigned_from_user_group';
                }
                break;
        }
        
        return '';
    }
    
    /**
     * Get document code based on table and transactionID
     * 
     * @param int|string $transactionID The ID to lookup
     * @param string $table Table name
     * @param string $crudType C, U, or D (used for context if needed)
     * @param string $locale Language code (not used for doc code, but kept for consistency)
     * @return string Document code or empty string if not found
     */
    public static function getDocCompanyCode($transactionID, $table, $parentID, $parentTable, $type = 'docCodeColumn')
    {
        try {
            $autoId = $transactionID;
            $docCodeInfo = self::getDocCodeMapping($table);
            
            if (empty($docCodeInfo)) {
                return '';
            }
            
            if (empty($docCodeInfo[$type])) {
                $autoId = $parentID;
                $docCodeInfo = self::getDocCodeMapping($parentTable);
                if (empty($docCodeInfo)) {
                    return '';
                }
                if (empty($docCodeInfo[$type])) {
                    return '';
                }
            }
            
            $record = DB::table($docCodeInfo['tableName'])
                ->where($docCodeInfo['primaryKey'], $autoId)
                ->select($docCodeInfo[$type])
                ->first();
            
            if ($record && isset($record->{$docCodeInfo[$type]})) {
                return $record->{$docCodeInfo[$type]};
            }
            
            return '';
        } catch (\Exception $e) {
            return '';
        }
    }
    
    /**
     * Get document code mapping for table
     * Maps table names to table name, primary key, and document code column
     * 
     * @param string $table
     * @return array Array with 'tableName', 'primaryKey', and 'docCodeColumn'
     */
    private static function getDocCodeMapping($table)
    {
        switch ($table) {
            case 'company_departments':
                return [
                    'tableName' => 'company_departments',
                    'primaryKey' => 'departmentSystemID',
                    'docCodeColumn' => 'departmentCode',
                    'companySystemIdColumn' => 'companySystemID'
                ];
                
            case 'serviceline':
                return [
                    'tableName' => 'serviceline',
                    'primaryKey' => 'serviceLineSystemID',
                    'docCodeColumn' => 'ServiceLineCode',
                    'companySystemIdColumn' => 'companySystemID'
                ];
                
            case 'chartofaccounts':
                return [
                    'tableName' => 'chartofaccounts',
                    'primaryKey' => 'chartOfAccountSystemID',
                    'docCodeColumn' => 'AccountCode',
                    'companySystemIdColumn' => 'primaryCompanySystemID'
                ];
                
            case 'suppliermaster':
                return [
                    'tableName' => 'suppliermaster',
                    'primaryKey' => 'supplierCodeSystem',
                    'docCodeColumn' => 'primarySupplierCode',
                    'companySystemIdColumn' => 'primaryCompanySystemID'
                ];
                
            case 'itemmaster':
                return [
                    'tableName' => 'itemmaster',
                    'primaryKey' => 'itemCodeSystem',
                    'docCodeColumn' => 'primaryCode',
                    'companySystemIdColumn' => 'primaryCompanySystemID'
                ];
                
            case 'customermaster':
                return [
                    'tableName' => 'customermaster',
                    'primaryKey' => 'customerCodeSystem',
                    'docCodeColumn' => 'CutomerCode',
                    'companySystemIdColumn' => 'primaryCompanySystemID'
                ];
                
            case 'erp_fa_asset_master':
                return [
                    'tableName' => 'erp_fa_asset_master',
                    'primaryKey' => 'faID',
                    'docCodeColumn' => 'faCode',
                    'companySystemIdColumn' => 'companySystemID'
                ];
                
            case 'department_budget_plannings':
                return [
                    'tableName' => 'department_budget_plannings',
                    'primaryKey' => 'id',
                    'docCodeColumn' => 'planningCode',
                    'companySystemIdColumn' => null // No direct column, gets via companyBudgetPlanning relationship
                ];
                
            case 'erp_workflow_configurations':
                return [
                    'tableName' => 'erp_workflow_configurations',
                    'primaryKey' => 'id',
                    'docCodeColumn' => 'workflowName',
                    'companySystemIdColumn' => 'companySystemID'
                ];
                
            case 'budget_templates':
                return [
                    'tableName' => 'budget_templates',
                    'primaryKey' => 'budgetTemplateID',
                    'docCodeColumn' => 'description',
                    'companySystemIdColumn' => 'companySystemID'
                ];
                
            case 'financeitemcategorysub':
                return [
                    'tableName' => 'financeitemcategorysub',
                    'primaryKey' => 'itemCategorySubID',
                    'docCodeColumn' => 'categoryDescription',
                    'companySystemIdColumn' => 'companySystemID'
                ];
                
            case 'erp_attributes':
                return [
                    'tableName' => 'erp_attributes',
                    'primaryKey' => 'id',
                    'docCodeColumn' => 'description',
                    'companySystemIdColumn' => null // No direct company column
                ];
                
            case 'financeitemcategorysubassigned':
                return [
                    'tableName' => 'financeitemcategorysubassigned',
                    'primaryKey' => 'itemCategoryAssignedID',
                    'docCodeColumn' => 'categoryDescription',
                    'companySystemIdColumn' => null // No direct company column
                ];
                
            case 'erp_fa_financecategory':
                return [
                    'tableName' => 'erp_fa_financecategory',
                    'primaryKey' => 'faFinanceCatID',
                    'docCodeColumn' => 'financeCatDescription',
                    'companySystemIdColumn' => 'companySystemID'
                ];
                
            case 'company_departments_segments':
                return [
                    'tableName' => 'company_departments_segments',
                    'primaryKey' => 'departmentSegmentSystemID',
                    'docCodeColumn' => null, // Junction table, no document code
                    'companySystemIdColumn' => null // Junction table, no direct company column
                ];
                
            case 'company_departments_employees':
                return [
                    'tableName' => 'company_departments_employees',
                    'primaryKey' => 'departmentEmployeeSystemID',
                    'docCodeColumn' => null, // Junction table, no document code
                    'companySystemIdColumn' => null // Junction table, no direct company column
                ];
                
            case 'department_budget_templates':
                return [
                    'tableName' => 'department_budget_templates',
                    'primaryKey' => 'departmentBudgetTemplateID',
                    'docCodeColumn' => null, // Junction table, no document code
                    'companySystemIdColumn' => null // Junction table, no direct company column
                ];
                
            case 'budget_template_columns':
                return [
                    'tableName' => 'budget_template_columns',
                    'primaryKey' => 'templateColumnID',
                    'docCodeColumn' => 'fieldCode',
                    'companySystemIdColumn' => null // No direct company column
                ];
                
            case 'erp_workflow_configuration_hod_actions':
                return [
                    'tableName' => 'erp_workflow_configuration_hod_actions',
                    'primaryKey' => 'id',
                    'docCodeColumn' => null, // Junction table, no document code
                    'companySystemIdColumn' => null // Junction table, no direct company column
                ];
                
            case 'department_budget_planning_details_template_data':
                return [
                    'tableName' => 'department_budget_planning_details_template_data',
                    'primaryKey' => 'id',
                    'docCodeColumn' => null, // No document code available
                    'companySystemIdColumn' => null // No direct company column
                ];
                
            case 'chart_of_account_config':
                return [
                    'tableName' => 'chart_of_account_config',
                    'primaryKey' => 'transactionID',
                    'docCodeColumn' => null, // No document code available
                    'companySystemIdColumn' => null // No direct company column
                ];
                
            case 'department_user_budget_control':
                return [
                    'tableName' => 'department_user_budget_control',
                    'primaryKey' => 'id',
                    'docCodeColumn' => null, // Junction/configuration table, no document code
                    'companySystemIdColumn' => null // Junction/configuration table, no direct company column
                ];

            case 'user_group':
                return [
                    'tableName' => 'srp_erp_usergroups',
                    'primaryKey' => 'userGroupID',
                    'docCodeColumn' => 'description',
                    'companySystemIdColumn' => 'companyID'
                ];

            case 'employee_navigation_assign':
                return [
                    'tableName' => 'srp_erp_employeenavigation',
                    'primaryKey' => 'id',
                    'docCodeColumn' => null, // No document code available
                    'companySystemIdColumn' => 'companyID'
                ];
                
            default:
                return [
                    'tableName' => $table,
                    'primaryKey' => 'id',
                    'docCodeColumn' => null,
                    'companySystemIdColumn' => null
                ];
        }
    }
}
