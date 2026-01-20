<?php

namespace App\Services;

use App\Models\TenderConfirmationDetail;
use Carbon\Carbon;

class TenderConfirmationService
{
    /**
     * Save confirmation details to the pivot table
     *
     * @param int $tenderId
     * @param int|null $referenceId
     * @param int $module
     * @param int|null $employeeId Optional, if not provided uses current employee
     * @param string|null $comment Optional comment
     * @return TenderConfirmationDetail
     */
    public static function saveConfirmationDetails($tenderId, $referenceId = null, $module, $employeeId = null, $comment = null)
    {
        if ($employeeId === null) {
            $employeeId = \Helper::getEmployeeSystemID();
        }

        $confirmationDetail = TenderConfirmationDetail::updateOrCreate(
            [
                'tender_id' => $tenderId,
                'reference_id' => $referenceId,
                'module' => $module
            ],
            [
                'action_by' => $employeeId,
                'action_at' => Carbon::now(),
                'comment' => $comment
            ]
        );

        return $confirmationDetail;
    }

    /**
     * Get confirmation details for a tender and module
     *
     * @param int $tenderId
     * @param int $module
     * @param int|null $referenceId Optional reference ID
     * @return TenderConfirmationDetail|null
     */
    public static function getConfirmationDetails($tenderId, $module, $referenceId = null)
    {
        $query = TenderConfirmationDetail::where('tender_id', $tenderId)
            ->where('module', $module);

        if ($referenceId !== null) {
            $query->where('reference_id', $referenceId);
        }

        return $query->with('actionByEmployee')->first();
    }
}
