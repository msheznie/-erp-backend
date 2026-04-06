<?php

namespace App\Services;

use App\Models\SupplierRegistrationLink;
use App\Models\SupplierTenderNegotiation;
use App\Models\TenderSupplierAssignee;
use App\Services\WebPushNotificationService;

class SrmNotificationService
{
    protected $webPushService;
    private const DOC_TYPES = [
        108 => 'Tender',
        113 => 'RFX',
    ];

    public function __construct(WebPushNotificationService $webPushService)
    {
        $this->webPushService = $webPushService;
    }

    public function sendOpenTenderInvitationNotification($tenderTitle, $companyId, $documentSystemID)
    {
        $suppliers = SupplierRegistrationLink::getFullyApprovedSuppliers($companyId);

        if ($suppliers->isEmpty()) {
            return;
        }

        $docType = $this->getDocType($documentSystemID);
        $path = (int) $documentSystemID === 108
            ? '/tender-management/tenders'
            : '/tender-management/rfx';

        foreach ($suppliers as $supplier) {
            $this->sendSupplierNotification(
                "New {$docType} {$tenderTitle} Published",
                '',
                    $path,
                $supplier->id,
                'Notification failed'
            );
        }
    }

    public function sendClosedOrSingleInvitationNotification($tenderTitle, $supplierRegistrationLinkId, $documentSystemID)
    {
        if (empty($supplierRegistrationLinkId)) {
            return;
        }

        $docType = $this->getDocType($documentSystemID);
        $path = (int) $documentSystemID === 108
            ? '/tender-management/tenders'
            : '/tender-management/rfx';

        $this->sendSupplierNotification(
            "New {$docType} {$tenderTitle} Published",
            '',
            $path,
            $supplierRegistrationLinkId,
            'Closed/single invitation notification failed'
        );
    }

    public function sendTenderDateChangedNotification(
        $tenderTitle,
        $companyId,
        $tenderMasterId,
        $tenderTypeId,
        $documentType,
        $documentSystemID
    ) {
        $docType = $this->getDocType($documentSystemID);
        $title = "{$docType} {$tenderTitle} Date Changed";
        $path = (int) $documentSystemID === 108
            ? '/tender-management/tenders'
            : '/tender-management/rfx';

        $supplierIds = collect();

        if ($tenderTypeId == 1 && $documentType == 0) {
            $supplierIds = SupplierRegistrationLink::getFullyApprovedSuppliers($companyId)->pluck('id');
        } else {
            $supplierIds = TenderSupplierAssignee::getSupplierAssignedForNotification($tenderMasterId);
        }

        if ($supplierIds->isEmpty()) {
            return;
        }

        foreach ($supplierIds as $supplierId) {
            $this->sendSupplierNotification(
                $title,
                '',
                $path,
                $supplierId,
                'Tender date changed notification failed'
            );
        }
    }

    public function sendNegotiationStartedNotification($tenderTitle, $documentSystemID, array $supplierIds)
    {
        $supplierIds = collect($supplierIds)
            ->filter(function ($id) {
                return !empty($id);
            })
            ->unique()
            ->values();

        if ($supplierIds->isEmpty()) {
            return;
        }

        $docType = $this->getDocType($documentSystemID);
        $title = "{$docType} {$tenderTitle} Negotiation Started";
        $path = (int) $documentSystemID === 108
            ? '/tender-management/tenders'
            : '/tender-management/rfx';

        foreach ($supplierIds as $supplierId) {
            $this->sendSupplierNotification(
                $title,
                '',
                $path,
                $supplierId,
                'Negotiation started notification failed'
            );
        }
    }

    public function sendNegotiationStartedNotificationByNegotiation($tenderNegotiationId, $tenderTitle, $documentSystemID)
    {
        $supplierIds = SupplierTenderNegotiation::getSupplierPickedForNegotiation($tenderNegotiationId);

        $this->sendNegotiationStartedNotification(
            $tenderTitle,
            $documentSystemID,
            $supplierIds
        );
    }

    public function sendTenderAwardedNotification($tenderTitle, $documentSystemID, $supplierRegistrationLinkId)
    {
        if (empty($supplierRegistrationLinkId)) {
            return;
        }

        $docType = $this->getDocType($documentSystemID);
        $title = "{$docType} {$tenderTitle} Awarded";
        $path = (int) $documentSystemID === 108
            ? '/tender-management/tenders'
            : '/tender-management/rfx';

        $this->sendSupplierNotification(
            $title,
            '',
            $path,
            $supplierRegistrationLinkId,
            'Tender awarded notification failed'
        );
    }

    public function sendDeliveryAppointmentStatusNotification(string $daNumber, bool $isApproved, $supplierRegistrationLinkId): void
    {
        if (empty($supplierRegistrationLinkId) || $daNumber === '') {
            return;
        }

        $title = $isApproved
            ? "Delivery Appointment {$daNumber} is approved"
            : "Delivery Appointment {$daNumber} is rejected";

        $url = "/delivery-appointment-all";

        $this->sendSupplierNotification(
            $title,
            '',
            $url,
            $supplierRegistrationLinkId,
            'Delivery appointment status notification failed'
        );
    }

    private function getDocType($documentSystemID): string
    {
        return self::DOC_TYPES[$documentSystemID] ?? 'Tender';
    }

    private function sendSupplierNotification(
        string $title,
        string $body,
        string $url,
        $supplierId,
        string $errorLogMessage
    ): void {
        try {
            $this->webPushService->sendNotification(
                [
                    'title' => $title,
                    'body' => $body,
                    'url' => $url,
                ],
                4,
                $supplierId,
                '',
                'supplier'
            );
        } catch (\Exception $e) {
            \Log::error($errorLogMessage, [
                'supplier_id' => $supplierId,
                'error' => $e->getMessage()
            ]);
        }
    }
}