<?php

namespace App\Jobs;

use App\Models\QuotationMaster;
use App\Models\ItemCategoryTypeMaster;
use App\Services\Sales\QuotationService;
use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\helper\CommonJobService;
use App\Models\ItemMaster;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class QuotationAddMultipleItemsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public $data;
    public $dispatch_db;
    
    // Job timeout - increase for large datasets
    public $timeout = 1800; // 30 minutes
    
    // Chunk size for processing items in batches
    protected $chunkSize = 250;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dispatch_db, $input)
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

        $this->data = $input;
        $this->dispatch_db = $dispatch_db;
        
        // Make chunk size configurable via environment or input
        $this->chunkSize = 250;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $db = $this->dispatch_db;

        Log::useFiles(storage_path() . '/logs/quotation_bulk_item.log');

        CommonJobService::db_switch($db);
        $input = $this->data;

        try {
            $totalProcessed = 0;
            $totalErrors = 0;
            $chunkNumber = 0;

            // Build the base query
            $baseQuery = $this->buildItemQuery($input);
            
            // Get total count for logging
            $totalItems = $baseQuery->count();

            // Process items in chunks
            $baseQuery->chunkById($this->chunkSize, function ($itemChunk) use (&$totalProcessed, &$totalErrors, &$chunkNumber, $input) {
                $chunkNumber++;
                $chunkProcessed = 0;
                $chunkErrors = 0;
                
                // Process each chunk in its own transaction
                DB::beginTransaction();
                try {
                    foreach ($itemChunk as $item) {
                        try {
                            // Validate item
                            $res = QuotationService::validateQuotationItem(
                                $item->itemCodeSystem, 
                                $input['companySystemID'], 
                                $input['quotationId']
                            );
                            
                            if ($res['status']) {
                                // Save item to quotation
                                QuotationService::saveQuotationItem(
                                    $item->itemCodeSystem, 
                                    $input['companySystemID'], 
                                    $input['quotationId'], 
                                    $input['empID'], 
                                    $input['employeeSystemID']
                                );
                                $chunkProcessed++;
                            } else {
                                Log::warning("Item validation failed", [
                                    'itemCode' => $item->itemCodeSystem,
                                    'reason' => $res['message'] ?? 'Unknown validation error'
                                ]);
                                $chunkErrors++;
                            }
                        } catch (\Exception $itemException) {
                            Log::error("Error processing individual item", [
                                'itemCode' => $item->itemCodeSystem,
                                'error' => $itemException->getMessage()
                            ]);
                            $chunkErrors++;
                        }
                    }
                    
                    DB::commit();
                    
                    $totalProcessed += $chunkProcessed;
                    $totalErrors += $chunkErrors;
                    
                    // Clear memory
                    unset($itemChunk);
                    
                    // Optional: Add a small delay to prevent overwhelming the database
                    if (env('QUOTATION_BULK_CHUNK_DELAY', 0) > 0) {
                        sleep(env('QUOTATION_BULK_CHUNK_DELAY', 0));
                    }
                    
                } catch (\Exception $chunkException) {
                    DB::rollBack();
                    Log::error("Error processing chunk {$chunkNumber}", [
                        'error' => $chunkException->getMessage(),
                        'chunk_size' => $itemChunk->count()
                    ]);
                    
                    // Continue with next chunk instead of failing entire job
                    $totalErrors += $itemChunk->count();
                }
            },'itemCodeSystem');

            // Final cleanup and status update
            $this->finalizeJob($input, $totalProcessed, $totalErrors);
            
        } catch (\Exception $exception) {
            Log::error('Critical error in QuotationAddMultipleItemsJob', [
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString()
            ]);
            
            // Ensure job status is updated even on failure
            $this->markJobAsFailed($input);
            
            throw $exception;
        }
    }

    /**
     * Build the base query for items
     */
    protected function buildItemQuery($input)
    {
        $companyId = $input['companySystemID'];
        
        return ItemMaster::whereHas('itemAssigned', function ($query) use ($companyId) {
            return $query->where('companySystemID', '=', $companyId)->where('isAssigned', -1);
        })->where('isActive', 1)
            ->where('itemApprovedYN', 1)
            ->where('financeCategoryMaster', '!=', 3) // Exclude fixed assets
            ->when((isset($input['financeCategoryMaster']) && $input['financeCategoryMaster']), function($query) use ($input){
                $query->where('financeCategoryMaster', $input['financeCategoryMaster']);
            })
            ->when((isset($input['financeCategorySub']) && $input['financeCategorySub']), function($query) use ($input){
                $query->where('financeCategorySub', $input['financeCategorySub']);
            })
            ->when((isset($input['searchTerm']) && $input['searchTerm']), function($query) use ($input){
                $searchTerm = $input['searchTerm'];
                $query->where(function ($query) use ($searchTerm) {
                    $query->where('primaryCode', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('secondaryItemCode', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('barcode', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('itemDescription', 'LIKE', "%{$searchTerm}%")
                        ->orWhereHas('unit', function ($q) use ($searchTerm) {
                            $q->where('UnitShortCode', 'LIKE', "%{$searchTerm}%");
                        });
                });
            })->whereHas('item_category_type', function ($query) {
                $query->whereIn('categoryTypeID', ItemCategoryTypeMaster::salesItems());
            })
            ->whereDoesntHave('quotationDetails', function($query) use ($input) {
                $query->where('quotationMasterID', $input['quotationId']);
            })
            ->with(['unit', 'unit_by', 'financeMainCategory', 'financeSubCategory', 'itemAssigned'])
            ->orderBy('itemCodeSystem'); // Ensure consistent ordering
    }

    /**
     * Finalize the job and update status
     */
    protected function finalizeJob($input, $totalProcessed, $totalErrors)
    {
        try {
            DB::beginTransaction();
            
            // Update the quotation master to mark job as complete
            QuotationMaster::where('quotationMasterID', $input['quotationId'])
                ->update(['isBulkItemJobRun' => 0]);
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error finalizing QuotationAddMultipleItemsJob', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Mark job as failed in case of critical error
     */
    protected function markJobAsFailed($input)
    {
        try {
            QuotationMaster::where('quotationMasterID', $input['quotationId'])
                ->update(['isBulkItemJobRun' => 0]);
        } catch (\Exception $e) {
            Log::error('Failed to update job status after critical error', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle job failure
     */
    public function failed(\Exception $exception)
    {
        Log::error('QuotationAddMultipleItemsJob failed completely', [
            'quotationId' => $this->data['quotationId'] ?? 'unknown',
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
        
        $this->markJobAsFailed($this->data);
    }
} 