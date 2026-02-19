<?php

namespace App\Jobs;

use App\helper\CommonJobService;
use App\Models\POSSOURCEShiftDetails;
use App\Models\ThirdPartyIntegrationKeys;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateFinancePostingStatusToPosJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const ENDPOINT_PATH = 'updateFinancePostingStatus';

    protected $dataBase;

    protected $shiftId;

    protected $status;

    protected $message;

    protected $postedAt;

    public function __construct($dataBase, int $shiftId, int $status, string $message = '', ?string $postedAt = null)
    {
        if (env('QUEUE_DRIVER_CHANGE', 'database') == 'database') {
            if (env('IS_MULTI_TENANCY', false)) {
                self::onConnection('database_main');
            } else {
                self::onConnection('database');
            }
        } else {
            self::onConnection(env('QUEUE_DRIVER_CHANGE', 'database'));
        }
        $this->dataBase = $dataBase;
        $this->shiftId = $shiftId;
        $this->status = $status;
        $this->message = $message;
        $this->postedAt = $postedAt;
    }

    public function handle(): void
    {
        CommonJobService::db_switch($this->dataBase);

        $shiftDetails = POSSOURCEShiftDetails::where('shiftID', $this->shiftId)
            ->select('companyID', 'posType')
            ->first();

        if (!$shiftDetails) {
            Log::channel('update_finance_posting_status_job')->error('UpdateFinancePostingStatusToPosJob: shift not found', ['shiftId' => $this->shiftId]);

            return;
        }

        $companyId = $shiftDetails->companyID;
        $posType = $shiftDetails->posType;

        if ($companyId === null || $companyId === '' || $posType === null || $posType === '') {
            Log::channel('update_finance_posting_status_job')->error('UpdateFinancePostingStatusToPosJob: shift companyID or posType is null or empty', [
                'shiftId' => $this->shiftId,
                'companyID' => $companyId,
                'posType' => $posType,
            ]);

            return;
        }

        $integrationKey = ThirdPartyIntegrationKeys::query()
            ->where('company_id', $companyId)
            ->where('third_party_system_id', $posType)
            ->where('status', 'Active')
            ->select('api_external_url', 'api_external_key')
            ->first();

        if (!$integrationKey) {
            Log::channel('update_finance_posting_status_job')->error('UpdateFinancePostingStatusToPosJob: no active POS integration key for shift', [
                'shiftId' => $this->shiftId,
                'companyId' => $companyId,
                'posType' => $posType,
            ]);

            return;
        }

        $apiExternalUrl = $integrationKey->api_external_url;
        $apiExternalKey = $integrationKey->api_external_key;

        if (trim((string) $apiExternalUrl) === '' || trim((string) $apiExternalKey) === '') {
            Log::channel('update_finance_posting_status_job')->error('UpdateFinancePostingStatusToPosJob: api_external_url or api_external_key is null or empty', [
                'shiftId' => $this->shiftId,
                'companyId' => $companyId,
            ]);

            return;
        }

        $url = rtrim($apiExternalUrl, '/') . '/' . self::ENDPOINT_PATH;
        $postedAt = $this->postedAt !== null && trim((string) $this->postedAt) !== ''
            ? $this->postedAt
            : Carbon::now()->format('Y-m-d H:i:s');

        $payload = [
            'shiftID' => $this->shiftId,
            'status' => $this->status,
            'message' => $this->message,
            'posted_at' => $postedAt,
        ];

        Log::channel('update_finance_posting_status_job')->info('UpdateFinancePostingStatusToPosJob: payload', ['payload' => $payload, 'url' => $url]);
        try {
            $client = new Client();
            $headers = [
                'Content-Type' => 'application/json',
                'Authorization' => 'ERP ' . $apiExternalKey,
            ];
            $client->request('POST', $url, [
                'headers' => $headers,
                'json' => $payload,
            ]);
        } catch (GuzzleException $e) {
            Log::channel('update_finance_posting_status_job')->error('UpdateFinancePostingStatusToPosJob: failed to notify POS', [
                'shiftId' => $this->shiftId,
                'url' => $url,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
