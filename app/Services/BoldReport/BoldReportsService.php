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

    public function getUserKey()
    {
        $this->client = new Client([
            'base_uri' => $this->boldReportsUrl.'/reporting/api/site/site1/',
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
}
