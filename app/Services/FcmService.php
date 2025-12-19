<?php

namespace App\Services;

use GuzzleHttp\Client;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Log;

class FcmService
{
    protected $fcmUrl;
    protected $credentials;

    public function __construct()
    {
        $this->fcmUrl = 'https://fcm.googleapis.com/v1/projects/' . env('FIREBASE_PROJECT_ID', 'gears-erp') . '/messages:send';

        $jsonKeyFile = storage_path('google-service-account.json');
        $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];

        if (file_exists($jsonKeyFile)) {
            $this->credentials = new ServiceAccountCredentials($scopes, $jsonKeyFile);
        }        
    }

    /**
     * Send a notification to FCM.
     *
     * @param array $deviceTokens
     * @param string $title
     * @param string $body
     * @param array|null $data
     * @return array
     */
    public function sendNotification(array $deviceTokens, string $title, string $body, array $data = null)
    {
        if (!file_exists(storage_path('google-service-account.json'))) {
            Log::error("google-service-account.json not found");
            return false;
        } 
        
        $accessToken = $this->getAccessToken();

        $headers = [
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type'  => 'application/json',
        ];

        $client = new Client();
        $responses = [];

        foreach ($deviceTokens as $token) {
            $payload = [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body'  => $body,
                    ],
                    'data' => array_map('strval', $data),
                ],
            ];

            try {
                $response = $client->post($this->fcmUrl, [
                    'headers' => $headers,
                    'json'    => $payload,
                ]);

                $responses[] = [
                    'status' => true,
                    'response' => json_decode($response->getBody(), true),
                ];
            } catch (\Exception $e) {
                $responses[] = [
                    'status' => false,
                    'error' => $e->getMessage(),
                    'token' => $token,
                ];
            }
        }

        return $responses;
    }

     protected function getAccessToken()
    {
        $authToken = $this->credentials->fetchAuthToken();
        if (isset($authToken['access_token'])) {
            return $authToken['access_token'];
        }

        throw new \Exception('Unable to fetch access token for FCM.');
    }
}
