<?php

namespace App\Jobs\POS;

use App\helper\CommonJobService;
use App\Jobs\InitiateWebhook;
use App\Services\POS\POSSourceWriterService;
use App\Validations\POS\ValidatePosSyncPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class POSSyncJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 500;

    public function __construct(
        public readonly array   $data,
        public readonly string  $db,
        public readonly string  $apiExternalKey,
        public readonly string  $apiExternalUrl,
        public readonly string  $webhookUrl,
        public readonly ?string $externalReference,
        public readonly ?string $tenantUuid,
        public readonly ?int    $companyId,
        public readonly int|string|null $logId,
        public readonly ?int    $thirdPartyIntegrationKeyId,
    ) {
        $connection = env('IS_MULTI_TENANCY', false) ? 'database_main' : 'database';
        self::onConnection(env('QUEUE_DRIVER_CHANGE', $connection));
    }

    public function handle(): void
    {
        CommonJobService::db_switch($this->db);

        $validation = ValidatePosSyncPayload::validate($this->data);

        if (!($validation['status'] ?? false)) {
            $returnData = [[
                'success' => false,
                'message' => 'POS shift payload validation failed',
                'code' => 422,
                'errors' => $validation['errors'] ?? [],
            ]];
            $this->fireWebhook(false, null, $returnData);
            return;
        }

        $writerErrors = POSSourceWriterService::validateBusinessRules($this->data);
        if ($writerErrors !== []) {
            $returnData = [[
                'success' => false,
                'message' => 'POS shift payload validation failed',
                'code' => 422,
                'errors' => $writerErrors,
            ]];
            $this->fireWebhook(false, null, $returnData);
            return;
        }

        DB::beginTransaction();
        try {
            POSSourceWriterService::write($this->data, $this->logId ?? 0);
            DB::commit();
            $returnData = [[
                'success' => true,
                'message' => 'POS sync completed Successfully!',
                'code' => 200,
                'data' => [
                    'type' => $this->data['type'] ?? null,
                ],
            ]];
            $this->fireWebhook(true, null, $returnData);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('[POSSyncJob] Failed', [
                'reference' => $this->externalReference,
                'error'     => $e->getMessage(),
                'trace'     => $e->getTraceAsString(),
            ]);
            $returnData = [[
                'success' => false,
                'message' => 'POS sync failed',
                'code' => 500,
                'errors' => [[
                    'field' => 'pos_sync',
                    'message' => [$e->getMessage()],
                ]],
            ]];
            $this->fireWebhook(false, $e->getMessage(), $returnData);
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('[POSSyncJob] Job failed permanently', [
            'reference' => $this->externalReference,
            'error'     => $exception->getMessage(),
        ]);
    }

    private function fireWebhook(bool $success, ?string $error = null, ?array $returnData = null): void
    {
        if (empty($this->webhookUrl)) {
            return;
        }

        $payload = [
            'event'        => 'pos.sync.' . ($success ? 'completed' : 'failed'),
            'processed_at' => now()->toIso8601String(),
            'error'        => $error,
            'data' => $returnData,
            'externalReference' => $this->externalReference,
        ];

        InitiateWebhook::dispatch(
            $this->db,
            $this->apiExternalKey,
            $this->apiExternalUrl,
            $this->webhookUrl,
            $payload,
            $this->externalReference,
            $this->tenantUuid,
            $this->companyId,
            $this->logId,
            $this->thirdPartyIntegrationKeyId
        );
    }
}