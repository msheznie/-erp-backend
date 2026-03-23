<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PosWebhookApiSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('third_party_apis')->insertOrIgnore([
            [
                'slug'             => 'pos-sync-callback',
                'name'             => 'POS Sync Callback',
                'webhook_endpoint' => '/pos/sync/callback',
                'description'      => 'Called by ERP when a POS shift sync job completes or fails. Payload contains event, log_id, shift_id, type, status, processed_at, and error.',
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
        ]);
    }
}
