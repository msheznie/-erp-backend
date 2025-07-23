<?php

namespace App\Jobs;
use App\helper\CommonJobService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\BankStatementMaster;
use App\Models\BankReconciliationRules;
use App\Models\BankLedger;
use App\Models\BankStatementDetail;
use Illuminate\Support\Facades\Log;
use App\Jobs\PaymentVoucherMatch;
use App\Jobs\ReceiptVoucherMatch;

class BankStatementMatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $db;
    protected $statementId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($db, $statementId)
    {
        if(env('QUEUE_DRIVER_CHANGE','database') == 'database'){
            if(env('IS_MULTI_TENANCY',false)){
                self::onConnection('database_main');
            }else{
                self::onConnection('database');
            }
        }else{
            self::onConnection(env('QUEUE_DRIVER_CHANGE','database'));
        }
        $this->db = $db;
        $this->statementId = $statementId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $db = $this->db;
        $statementId = $this->statementId;
        CommonJobService::db_switch($db);
        Log::useFiles(storage_path().'/logs/bank_statement_match.log');
        Log::info("Payment Voucher Match");
        PaymentVoucherMatch::dispatch($db, $statementId);
    }
}
