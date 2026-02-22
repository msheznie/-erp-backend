<?php

namespace App\Services\Budget;

use App\Http\Controllers\API\DepartmentBudgetPlanningDetailAPIController;
use App\Services\WebPushNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ExportCompanyBudgetPlanningDetailsExcel
{
    private $input;
    private $userId;

    public function __construct(array $input, $userId)
    {
        $this->input = $input;
        $this->userId = $userId;
    }

    /**
     * Run export and send notification when done.
     *
     * @return array
     */
    public function export()
    {
        $request = new Request();
        $request->merge($this->input);
        $request->merge([
            'source' => 'from_approval',
            'isCompany' => true,
        ]);

        try {
            $controller = app(DepartmentBudgetPlanningDetailAPIController::class);
            $basePath = $controller->runExportBudgetPlanningDetails($request);
            Log::info('Budget planning export completed', ['path' => $basePath]);
            $this->sendNotification($basePath);
            return ['success' => true, 'message' => trans('custom.success_export')];
        } catch (\Exception $e) {
            Log::error('Budget planning export failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    private function sendNotification($basePath)
    {
        $webPushData = [
            'title' => 'budget_planning_export_ready',
            'body' => '',
            'url' => '',
            'path' => $basePath,
        ];
        return WebPushNotificationService::sendNotification($webPushData, 3, [$this->userId]);
    }
}
