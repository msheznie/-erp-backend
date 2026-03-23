<?php

namespace App\Jobs\POS;

use App\helper\CommonJobService;
use App\Jobs\InitiateWebhook;
use App\Services\POS\POSSourceWriterService;
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
        public readonly ?int    $logId,
        public readonly ?int    $thirdPartyIntegrationKeyId,
    ) {
        $connection = env('IS_MULTI_TENANCY', false) ? 'database_main' : 'database';
        self::onConnection(env('QUEUE_DRIVER_CHANGE', $connection));
    }

    public function handle(): void
    {
        CommonJobService::db_switch($this->db);

        DB::beginTransaction();
        try {
            POSSourceWriterService::write($this->data, $this->logId ?? 0);
            DB::commit();
            $this->fireWebhook(true);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('[POSSyncJob] Failed', [
                'reference' => $this->externalReference,
                'error'     => $e->getMessage(),
                'trace'     => $e->getTraceAsString(),
            ]);
            $this->fireWebhook(false, $e->getMessage());
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('[POSSyncJob] Job failed permanently', [
            'reference' => $this->externalReference,
            'error'     => $exception->getMessage(),
        ]);
    }

    private function fireWebhook(bool $success, ?string $error = null): void
    {
        if (empty($this->webhookUrl)) {
            return;
        }

        $payload = [
            'event'        => 'pos.sync.' . ($success ? 'completed' : 'failed'),
            'reference'    => $this->externalReference,
            'shift_id'     => $this->data['shift']['shiftID'] ?? null,
            'type'         => $this->data['type'] ?? null,
            'status'       => $success ? 'completed' : 'failed',
            'processed_at' => now()->toIso8601String(),
            'error'        => $error,
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
