<?php
namespace App\Services;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class LokiService
{

    public function getAuditLogs($params){
        try {

            $client = new Client();
            $url = env("LOKI_URL");

            $response = $client->get($url . $params);

            $data = json_decode($response->getBody()->getContents(), true);

            $logEntriesAsArrays = array_map(function ($entry) {
                // $entry['metric']['log'] = $this->extractJsonFromLog($entry['metric']['log']);
                return $entry;
            }, $data['data']['result']);


            usort($logEntriesAsArrays, function ($a, $b) {
                $timestampA = strtotime(isset($a['metric']['date_time']) ? $a['metric']['date_time']: null);
                $timestampB = strtotime(isset($b['metric']['date_time']) ? $b['metric']['date_time']: null);

                return $timestampB - $timestampA;
            });


            return $logEntriesAsArrays;

        } catch (RequestException $e) {
            \Log::error('Loki connection error: ' . $e->getMessage());
            
            if ($e->hasResponse()) {
                $statusCode = $e->getResponse()->getStatusCode();
                $errorBody = $e->getResponse()->getBody()->getContents();
                return response()->json(['error' => "HTTP $statusCode: $errorBody"], $statusCode);
            } else {
                return response()->json(['error' => 'Request failed: ' . $e->getMessage()], 500);
            }
        } catch (\Exception $e) {
            \Log::error('Loki service error: ' . $e->getMessage());
            return [];
        }

    }

    public function extractJsonFromLog($logEntry)
    {
        preg_match('/{.*}/', $logEntry, $matches);

        if (!empty($matches)) {
            return json_decode($matches[0], true);
        }

        return [];
    }

    public function getAuditTables($module){
        switch ($module) {
            case 'item_finance_sub_category':
                $table = 'financeitemcategorysub';
                break;
            case 'customer':
                $table = 'customermaster';
                break;
            case 'supplier':
                $table = 'suppliermaster';
                break;
            case 'chartOfAccount':
                $table = 'chartofaccounts';
                break;
            case 'item':
                $table = 'itemmaster';
                break;
            case 'asset_finance_category':
                $table = 'erp_fa_financecategory';
                break;
            case 'chart_of_account_config':
                $table = 'chart_of_account_config';
                break;
            case 'asset_costing':
                $table = 'erp_fa_asset_master';
                break;
            case 'serviceline':
                $table = 'serviceline';
                break;
            case 'asset_costing_attributes':
                $table = 'erp_attributes';
                break;
            case 'company_departments':
                $table = 'company_departments';
                break;
            case 'department_budget_templates':
                $table = 'department_budget_templates';
                break;
            case 'budget_templates':
                $table = 'budget_templates';
                break;
            case 'budget_template_columns':
                $table = 'budget_template_columns';
                break;
            case 'erp_workflow_configuration':
                $table = 'erp_workflow_configurations';
                break;
            case 'department_budget_plannings':
                $table = 'department_budget_plannings';
                break;
            case 'department_budget_planning_details_template_data':
                $table = 'department_budget_planning_details_template_data';
                break;
            default:
                $table = null;
                break;
        }

        return $table;
    }

    /**
     * Get all table names for audit log migration
     * 
     * @return array
     */
    public function getAllAuditTables(){
        return [
            'financeitemcategorysub',
            'customermaster',
            'suppliermaster',
            'chartofaccounts',
            'itemmaster',
            'erp_fa_financecategory',
            'chart_of_account_config',
            'erp_fa_asset_master',
            'serviceline',
            'erp_attributes',
            'company_departments',
            'department_budget_templates',
            'budget_templates',
            'budget_template_columns',
            'erp_workflow_configurations',
            'department_budget_plannings',
            'department_budget_planning_details_template_data',
        ];
    }

    public function getAuditLogsForMigration($params){
        try {

            $client = new Client();
            $url = env("LOKI_URL");

            $response = $client->get($url . $params);

            $data = json_decode($response->getBody()->getContents(), true);

            $logEntriesAsArrays = array_map(function ($entry) {
                $entry['metric']['log'] = $this->extractJsonFromLog($entry['metric']['log']);
                return $entry['metric']['log'];
            }, $data['data']['result']);

            return $logEntriesAsArrays;

        } catch (RequestException $e) {
            \Log::error('Loki connection error (migration): ' . $e->getMessage());
            
            if ($e->hasResponse()) {
                $statusCode = $e->getResponse()->getStatusCode();
                $errorBody = $e->getResponse()->getBody()->getContents();
                return response()->json(['error' => "HTTP $statusCode: $errorBody"], $statusCode);
            } else {
                return response()->json(['error' => 'Request failed: ' . $e->getMessage()], 500);
            }
        } catch (\Exception $e) {
            \Log::error('Loki service error (migration): ' . $e->getMessage());
            return [];
        }

    }
}
