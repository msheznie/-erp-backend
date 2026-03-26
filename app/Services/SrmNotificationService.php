<?php

namespace App\Services;

use App\Models\SupplierRegistrationLink;
use App\Services\WebPushNotificationService;

class SrmNotificationService
{
    protected $webPushService;

    public function __construct(WebPushNotificationService $webPushService)
    {
        $this->webPushService = $webPushService;
    }

    public function sendOpenTenderInvitationNotification($tenderTitle, $companyId, $urlString, $documentSystemID)
    {
        $suppliers = SupplierRegistrationLink::getFullyApprovedSuppliers($companyId);

        if ($suppliers->isEmpty()) {
            return;
        }

        $types = [
            108 => 'Tender',
            113 => 'RFX',
        ];

        $docType = $types[$documentSystemID] ?? 'Tender';

        $baseUrl = rtrim($urlString, '/');

        $path = $documentSystemID == 108
            ? '/tender-management/tenders'
            : '/tender-management/rfx';

        $full_url = $baseUrl . $path;

        foreach ($suppliers as $supplier) {
            try {
                $this->webPushService->sendNotification(
                    [
                        'title' => "New {$docType} {$tenderTitle} Published",
                        'body' => "",
                        'url' => $full_url,
                    ],
                    4,
                    $supplier->id,
                    '',
                    'supplier'
                );
            } catch (\Exception $e) {
                \Log::error('Notification failed', [
                    'supplier_id' => $supplier->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    public function sendClosedOrSingleInvitationNotification($tenderTitle, $supplierRegistrationLinkId, $urlString, $documentSystemID)
    {
        if (empty($supplierRegistrationLinkId)) {
            return;
        }

        $types = [
            108 => 'Tender',
            113 => 'RFX',
        ];

        $docType = $types[$documentSystemID] ?? 'Tender';
        $baseUrl = rtrim($urlString, '/');
        $path = $documentSystemID == 108
            ? '/tender-management/tenders'
            : '/tender-management/rfx';
        $fullUrl = $baseUrl . $path;

        try {
            $this->webPushService->sendNotification(
                [
                    'title' => "Invitation for {$docType} {$tenderTitle}",
                    'body' => '',
                    'url' => $fullUrl,
                ],
                4,
                $supplierRegistrationLinkId,
                '',
                'supplier'
            );
        } catch (\Exception $e) {
            \Log::error('Closed/single invitation notification failed', [
                'supplier_id' => $supplierRegistrationLinkId,
                'error' => $e->getMessage()
            ]);
        }
    }
}