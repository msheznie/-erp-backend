<?php

namespace App\Services\BoldReport;

use GuzzleHttp\Client;
use Illuminate\Support\Str;

class BoldReportsService
{
    protected $client;
    private $tokenUrl = "/reporting/api/site/site1/token";
    private $boldReportsUrl = "https://erp-portal-qa.gears-int.com"; // Your Bold Reports URL
    private $username = "demo@boldreports.com"; // Your Email ID
    private $secretCode = "ZNZcbL955rWrvAQTxqjgPzPKPb3xbWt"; // Embed secret key value

    public function getUserKey($tenant = 'osos-qa')
    {
        $this->client = new Client([
            'base_uri' => $this->boldReportsUrl.'/reporting/api/site/'.$tenant.'/',
            'timeout'  => 10.0,
        ]);

        try {
            $response = $this->client->post('get-user-key', [
                'form_params' => [
                    'password' => 'Admin@123',              // Provide your Password
                    'userid'   => $this->username,  // Provide your Email ID
                ]
            ]);

            $body = $response->getBody()->getContents();

            // API returns something like: {"Token":"{...}"}
            $data = json_decode($body, true);

            if (isset($data['Token'])) {
                // The "Token" itself may be a JSON string, so decode again
                return json_decode($data['Token'], true);
            }

            return $data;

        } catch (\Exception $e) {
            \Log::error('Report API error: ' . $e->getMessage());
            return null;
        }
    }


    public function getToken()
    {
        $nonce = Str::random(32); // Generate random nonce (equivalent to Guid.NewGuid())
        $timeStamp = $this->dateTimeToUnixTimeStamp(now());

        $embedMessage = implode('&', [
            'embed_nonce=' . $nonce,
            'user_email=' . $this->username,
            'timestamp=' . $timeStamp
        ]);

        $signature = $this->signURL(strtolower($embedMessage), $this->secretCode);

        $client = new Client([
            'base_uri' => $this->boldReportsUrl,
            'http_errors' => false,
            'headers' => [
                'Accept' => '*/*',
                'Connection' => 'close'
            ]
        ]);

        $response = $client->post($this->tokenUrl, [
            'form_params' => [
                'grant_type'      => 'embed_secret',
                'username'        => $this->username,
                'embed_nonce'     => $nonce,
                'embed_signature' => $signature,
                'timestamp'       => $timeStamp
            ]
        ]);

        // Debug: Log the request parameters
        \Log::info('Request parameters:', [
            'grant_type' => 'embed_secret',
            'username' => $this->username,
            'embed_nonce' => $nonce,
            'embed_signature' => $signature,
            'timestamp' => $timeStamp
        ]);

        $resultContent = json_decode($response->getBody()->getContents(), true);

        // Check for authorization failure like in C# code
        if (isset($resultContent['error']) && $resultContent['error'] === 'authorization_failed') {
            // Log the error (equivalent to Console.WriteLine in C#)
            \Log::error("authorization_failed: " . ($resultContent['error_description'] ?? 'Unknown error'));
            
            return [
                'error' => 'authorization_failed',
                'error_description' => $resultContent['error_description'] ?? 'Unknown error'
            ];
        }

        // Return the token response matching the Token class structure
        return [
            'access_token' => $resultContent['access_token'] ?? null,
            'token_type' => $resultContent['token_type'] ?? null,
            'expires_in' => $resultContent['expires_in'] ?? null,
            'email' => $resultContent['email'] ?? null,
            'error' => $resultContent['error'] ?? null,
            'error_description' => $resultContent['error_description'] ?? null
        ];
    }

    private function dateTimeToUnixTimeStamp($dateTime)
    {
        // Convert to UTC like in C# code
        return $dateTime->setTimezone('UTC')->timestamp;
    }

    private function signURL($embedMessage, $secretCode)
    {
        // Use HMAC-SHA256 like in C# code
        return base64_encode(hash_hmac('sha256', $embedMessage, $secretCode, true));
    }

    /**
     * Get all reports from Bold Reports server
     * @param string $tenant - Optional tenant parameter for multi-tenancy
     * @return array|null
     */
    public function getAllReports($tenant = 'site1')
    {
        try {
            // First get authentication token
            $tokenResponse = $this->getUserKey($tenant);
            
            if (!isset($tokenResponse['access_token'])) {
                \Log::error('Authentication failed: ');
                return null;
            }

            $client = new Client([
                'base_uri' => $this->boldReportsUrl,
                'headers' => [
                    'Authorization' => 'bearer ' . $tokenResponse['access_token'],
                    'Content-Type' => 'application/json'
                ]
            ]);

            // Get report summary data from Bold Reports server
            $response = $client->get("/reporting/api/site/{$tenant}/v5.0/items", [
                'query' => [
                    'itemType' => 'Report'
                ]
            ]);
            
            if ($response->getStatusCode() !== 200) {
                return null;
            }

            $summaryData = json_decode($response->getBody()->getContents(), true);
            
            // Get detailed data for each report
            $detailedReports = [];
            foreach ($summaryData as $reportSummary) {
                if (isset($reportSummary['ReportId'])) {
                    $reportId = $reportSummary['ReportId'];
                    
                    try {
                        // Get detailed information for each report
                        $detailResponse = $client->get("/reporting/api/site/{$tenant}/v5.0/items/{$reportId}");
                        
                        if ($detailResponse->getStatusCode() === 200) {
                            $detailData = json_decode($detailResponse->getBody()->getContents(), true);
                            $detailData['summary'] = $reportSummary;
                            $detailedReports[] = $detailData;
                        } 
                    } catch (\Exception $e) {
                        \Log::warning("Failed to fetch details for report ID {$reportId}: " . $e->getMessage());
                        // Fall back to summary data if detail call fails
                        $detailedReports[] = $reportSummary;
                    }
                }
            }

            return $detailedReports;    
        } catch (\Exception $e) {
            \Log::error('Get reports error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get report by ID from Bold Reports server
     * @param string $reportId
     * @param string $tenant
     * @return array|null
     */
    public function getReportById($reportId, $tenant = 'site1')
    {
        try {
            // First get authentication token
            $tokenResponse = $this->getUserKey($tenant);
            
            if (!isset($tokenResponse['access_token'])) {
                \Log::error('Authentication failed: ');
                return null;
            }

            $client = new Client([
                'base_uri' => $this->boldReportsUrl,
                'timeout'  => 30.0,
                'headers' => [
                    'Authorization' => 'bearer ' . $tokenResponse['access_token'],
                    'Content-Type' => 'application/json'
                ]
            ]);

            // Get specific report from Bold Reports server
            $response = $client->get("/reporting/api/site/{$tenant}/v5.0/items/{$reportId}");
            
            if ($response->getStatusCode() === 200) {
                $body = $response->getBody()->getContents();
                $report = json_decode($body, true);
                
                $reportData = [];
                $reportData['access_token'] = 'bearer '.$tokenResponse['access_token'];
                $reportData['reportName'] = $report['Name'];
                $reportData['reportPath'] = '/'.$report['CategoryName'].'/'.$report['Name'];
                $reportData['reportServerUrl'] = $this->boldReportsUrl.'/reporting/api/site/'.$tenant;
                $reportData['serviceUrl'] = $this->boldReportsUrl.'/reporting/reportservice/api/Viewer';

                return $reportData;
            }

            return null;

        } catch (\Exception $e) {
            \Log::error('Get report by ID error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get available tenants from Bold Reports server
     * @return array|null
     */
    public function getTenants()
    {
        try {
            // First get authentication token
            $tokenResponse = $this->getUserKey();
            
            if (isset($tokenResponse['error'])) {
                \Log::error('Authentication failed: ' . $tokenResponse['error_description']);
                return null;
            }

            $client = new Client([
                'base_uri' => $this->boldReportsUrl,
                'timeout'  => 30.0,
                'headers' => [
                    'Authorization' => 'Bearer ' . $tokenResponse['access_token'],
                    'Content-Type' => 'application/json'
                ]
            ]);

            // Get tenants from Bold Reports server
            $response = $client->get("/reporting/api/sites");
            
            if ($response->getStatusCode() === 200) {
                $body = $response->getBody()->getContents();
                return json_decode($body, true);
            }

            return null;

        } catch (\Exception $e) {
            \Log::error('Get tenants error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get report categories from Bold Reports server
     * @param string $tenant
     * @return array|null
     */
    public function getReportCategories($tenant = 'site1')
    {
        try {
            // First get authentication token
            $tokenResponse = $this->getUserKey();
            
            if (isset($tokenResponse['error'])) {
                \Log::error('Authentication failed: ' . $tokenResponse['error_description']);
                return null;
            }

            $client = new Client([
                'base_uri' => $this->boldReportsUrl,
                'timeout'  => 30.0,
                'headers' => [
                    'Authorization' => 'Bearer ' . $tokenResponse['access_token'],
                    'Content-Type' => 'application/json'
                ]
            ]);

            // Get categories from Bold Reports server
            $response = $client->get("/reporting/api/site/{$tenant}/categories");
            
            if ($response->getStatusCode() === 200) {
                $body = $response->getBody()->getContents();
                return json_decode($body, true);
            }

            return null;

        } catch (\Exception $e) {
            \Log::error('Get report categories error: ' . $e->getMessage());
            return null;
        }
    }
}
