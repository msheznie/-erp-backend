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
                $entry['metric']['log'] = $this->extractJsonFromLog($entry['metric']['log']);
                return $entry;
            }, $data['data']['result']);

            usort($logEntriesAsArrays, function ($a, $b) {
                $timestampA = strtotime($a['metric']['log']['date_time']);
                $timestampB = strtotime($b['metric']['log']['date_time']);

                return $timestampB - $timestampA;
            });


            return $logEntriesAsArrays;

        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $statusCode = $e->getResponse()->getStatusCode();
                $errorBody = $e->getResponse()->getBody()->getContents();
                return response()->json(['error' => "HTTP $statusCode: $errorBody"], $statusCode);
            } else {
                return response()->json(['error' => 'Request failed: ' . $e->getMessage()], 500);
            }
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
            default:
                $table = null;
                break;
        }

        return $table;
    }


}
