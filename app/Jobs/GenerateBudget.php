<?php

namespace App\Jobs;

use App\Models\CompanyBudgetPlanningGenerate;
use App\Services\GenerateCompanyBudgetPlanningService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class GenerateBudget implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var int|null Optional company budget planning ID to scope pending items; null = all pending */
    protected $budgetPlanningId;

    /**
     * Create a new job instance.
     *
     * @param int|null $budgetPlanningId Optional. If set, only process pending rows for this plan.
     */
    public function __construct($budgetPlanningId = null)
    {
        $this->budgetPlanningId = $budgetPlanningId;
    }

    /**
     * Execute the job: generate all not-generated budget rows (optionally for one plan).
     */
    public function handle(GenerateCompanyBudgetPlanningService $generateService)
    {
        $query = CompanyBudgetPlanningGenerate::where('is_generated', false);

        if ($this->budgetPlanningId !== null) {
            $query->where('company_budget_planning_id', $this->budgetPlanningId);
        }

        $items = $query->orderBy('id')->get();

        foreach ($items as $item) {
            try {
                $generateService->generate($item->row_id);
                $item->is_generated = true;
                $item->save();
            } catch (\Exception $e) {
                Log::useFiles(storage_path() . '/logs/budget_planning_generate.log');
                Log::error("row id: " . $item->row_id);
                Log::error('Error generating budget: ' . $e->getMessage());
                continue;
            }
        }
    }
}
