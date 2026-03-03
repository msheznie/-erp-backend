<?php

namespace App\Services;

use App\helper\email as Email;
use App\helper\Helper;
use App\Models\BidBoq;
use App\Models\BidMainWork;
use App\Models\BidSubmissionMaster;
use App\Models\PricingScheduleDetail;
use App\Models\SrmItemWiseTenderAwarding;
use App\Models\SupplierRegistrationLink;
use App\Models\TenderBoqItems;
use App\Models\TenderConfirmationDetail;
use App\Models\TenderMaster;
use App\Models\TenderNegotiation;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class TenderItemWiseAwardingService
{
    /** @var TenderCommercialBidService */
    protected $commercialBidService;

    public function __construct(TenderCommercialBidService $commercialBidService)
    {
        $this->commercialBidService = $commercialBidService;
    }

    /**
     * Resolve is_negotiation (0 or 1) from tender.
     */
    public static function resolveIsNegotiation(TenderMaster $tender): int
    {
        return ($tender->negotiation_code != '' && $tender->negotiation_code !== null) ? 1 : 0;
    }

    public function buildItemWiseAwardingData(int $tenderId, int $isNegotiation): array
    {
        $bidMasterId = $this->commercialBidService->getCommercialBids($tenderId, $isNegotiation);
        if (empty($bidMasterId) || !is_array($bidMasterId)) {
            $bidMasterId = $bidMasterId && method_exists($bidMasterId, 'toArray') ? $bidMasterId->toArray() : [];
        }
        if (empty($bidMasterId)) {
            return [
                'bid_submissions' => collect([]),
                'items' => collect([]),
                'evaluation_type_id' => 1,
                'item_wise_awarding' => [],
            ];
        }

        $items = $this->commercialBidService->getPricingItems($bidMasterId, $tenderId);
        $bidSubmissions = BidSubmissionMaster::with(['SupplierRegistrationLink' => function ($q) {
            $q->with(['supplier' => function ($sq) {
                $sq->select('supplierCodeSystem', 'approvedYN', 'supplierConfirmedYN', 'isActive');
            }]);
        }])->whereIn('id', $bidMasterId)->where('tender_id', $tenderId)->get();

        $existing = SrmItemWiseTenderAwarding::forTenderNegotiation($tenderId, $isNegotiation)->get();
        $awardMap = SrmItemWiseTenderAwarding::getAwardMapForTender($tenderId, $isNegotiation);

        foreach ($items as $scheduleMaster) {
            foreach ($scheduleMaster->pricing_shedule_details ?? [] as $detail) {
                $works = $detail->bid_main_works ?? [];
                if (count($works) === 0 && (!isset($detail->tender_boq_items) || count($detail->tender_boq_items ?? []) === 0)) {
                    $lineValue = isset($detail->ranking_items->value) ? (float) $detail->ranking_items->value : 0;
                    $works = [];
                    foreach ($bidSubmissions as $bid) {
                        $works[] = (object) [
                            'bid_master_id' => $bid->id,
                            'total_amount' => $lineValue,
                            'amount' => $lineValue,
                            'system_pick' => false,
                            'award' => false,
                        ];
                    }
                    $detail->setRelation('bid_main_works', collect($works));
                }
                $works = $detail->bid_main_works ?? [];
                if (count($works) > 0) {
                    $minAmount = null;
                    $minBidId = null;
                    foreach ($works as $w) {
                        $amt = isset($w->total_amount) ? (float) $w->total_amount : (float) ($w->amount ?? 0);
                        if ($minAmount === null || $amt < $minAmount) {
                            $minAmount = $amt;
                            $minBidId = $w->bid_master_id;
                        }
                    }
                    foreach ($works as $w) {
                        $w->system_pick = ($w->bid_master_id == $minBidId);
                        $key = 'main_' . $detail->id;
                        $w->award = isset($awardMap[$key]) && (int) $awardMap[$key]['bid_id'] === (int) $w->bid_master_id;
                    }
                }
                foreach ($detail->tender_boq_items ?? [] as $boqItem) {
                    $boqs = $boqItem->bid_boqs ?? [];
                    if (count($boqs) > 0) {
                        $minAmount = null;
                        $minBidId = null;
                        foreach ($boqs as $bq) {
                            $amt = (float) ($bq->total_amount ?? 0);
                            if ($minAmount === null || $amt < $minAmount) {
                                $minAmount = $amt;
                                $minBidId = $bq->bid_master_id;
                            }
                        }
                        foreach ($boqs as $bq) {
                            $bq->system_pick = ($bq->bid_master_id == $minBidId);
                            $key = 'boq_' . $boqItem->id;
                            $bq->award = isset($awardMap[$key]) && (int) $awardMap[$key]['bid_id'] === (int) $bq->bid_master_id;
                        }
                    }
                }
            }
        }

        $tender = TenderMaster::where('id', $tenderId)->select('evaluation_type_id')->first();
        return [
            'bid_submissions' => $bidSubmissions,
            'items' => $items,
            'evaluation_type_id' => $tender ? (int) $tender->evaluation_type_id : 1,
            'item_wise_awarding' => $existing->toArray(),
        ];
    }

    public function getItemWiseSystemPickBidMap(int $tenderId, int $isNegotiation): array
    {
        $bidMasterId = $this->commercialBidService->getCommercialBids($tenderId, $isNegotiation);
        if (empty($bidMasterId) || !is_array($bidMasterId)) {
            $bidMasterId = $bidMasterId && method_exists($bidMasterId, 'toArray') ? $bidMasterId->toArray() : [];
        }
        if (empty($bidMasterId)) {
            return [];
        }
        $items = $this->commercialBidService->getPricingItems($bidMasterId, $tenderId);
        $bidSubmissions = BidSubmissionMaster::whereIn('id', $bidMasterId)->where('tender_id', $tenderId)->get();
        $map = [];
        foreach ($items as $scheduleMaster) {
            foreach ($scheduleMaster->pricing_shedule_details ?? [] as $detail) {
                $works = $detail->bid_main_works ?? [];
                if (count($works) === 0 && (!isset($detail->tender_boq_items) || count($detail->tender_boq_items ?? []) === 0)) {
                    $lineValue = isset($detail->ranking_items->value) ? (float) $detail->ranking_items->value : 0;
                    $works = [];
                    foreach ($bidSubmissions as $bid) {
                        $works[] = (object) ['bid_master_id' => $bid->id, 'total_amount' => $lineValue, 'amount' => $lineValue];
                    }
                }
                if (count($works) > 0) {
                    $minAmount = null;
                    $minBidId = null;
                    foreach ($works as $w) {
                        $amt = isset($w->total_amount) ? (float) $w->total_amount : (float) ($w->amount ?? 0);
                        if ($minAmount === null || $amt < $minAmount) {
                            $minAmount = $amt;
                            $minBidId = $w->bid_master_id;
                        }
                    }
                    $map['main_' . $detail->id] = $minBidId;
                }
                foreach ($detail->tender_boq_items ?? [] as $boqItem) {
                    $boqs = $boqItem->bid_boqs ?? [];
                    if (count($boqs) > 0) {
                        $minAmount = null;
                        $minBidId = null;
                        foreach ($boqs as $bq) {
                            $amt = (float) ($bq->total_amount ?? 0);
                            if ($minAmount === null || $amt < $minAmount) {
                                $minAmount = $amt;
                                $minBidId = $bq->bid_master_id;
                            }
                        }
                        $map['boq_' . $boqItem->id] = $minBidId;
                    }
                }
            }
        }
        return $map;
    }

    public function confirmItemWiseCombinedRanking(Request $request): array
    {
        try {
            return DB::transaction(function () use ($request) {

                $tenderId     = $request['tenderMasterId'];
                $isNegotiation = (int) ($request['isNegotiation'] ?? 0);
                $comment      = $request['comment'] ?? '';
                $selections   = $request['selections'] ?? [];
                $userId       = $request->user()->id ?? null;

                $bidMasterId = $this->commercialBidService
                    ->getCommercialBids($tenderId, $isNegotiation);

                $bidMasterId = $this->normalizeToArray($bidMasterId);

                SrmItemWiseTenderAwarding::clearAwardForTenderNegotiation(
                    $tenderId,
                    $isNegotiation
                );

                $systemPickMap = $this->getItemWiseSystemPickBidMap(
                    $tenderId,
                    $isNegotiation
                );

                foreach ($selections as $sel) {

                    $bidFormatDetailId = isset($sel['bid_format_detail_id'])
                        ? (int) $sel['bid_format_detail_id']
                        : null;

                    $boqItemId = isset($sel['boq_item_id'])
                        ? (int) $sel['boq_item_id']
                        : null;

                    $bidId      = (int) ($sel['bid_id'] ?? 0);
                    $supplierId = (int) ($sel['supplier_id'] ?? 0);
                    $bidAmount  = $sel['bid_amount'] ?? null;

                    if (!$bidId || !$supplierId) {
                        continue;
                    }

                    $bidFormatDetailId = $bidFormatDetailId ?: null;
                    $boqItemId         = $boqItemId ?: null;

                    $key = $boqItemId
                        ? 'boq_' . $boqItemId
                        : 'main_' . $bidFormatDetailId;

                    $isSystemPick = isset($systemPickMap[$key])
                        && (int) $systemPickMap[$key] === $bidId;

                    $this->saveItemWiseAwarding(
                        $tenderId,
                        $bidFormatDetailId,
                        $boqItemId,
                        $bidId,
                        $supplierId,
                        $bidAmount,
                        $isSystemPick,
                        $isNegotiation,
                        $userId
                    );
                }

                TenderMaster::updateCombinedRankingStatus(
                    $tenderId,
                    $isNegotiation,
                    $comment
                );

                $tenderNegotiationId = null;

                if ($isNegotiation === 1) {
                    $latestNegotiation = TenderNegotiation
                        ::getTenderLatestNegotiations($tenderId);

                    $tenderNegotiationId = $latestNegotiation->id ?? null;
                }

                TenderConfirmationService::saveConfirmationDetails(
                    $tenderId,
                    $tenderId,
                    TenderConfirmationDetail::MODULE_COMBINED_RANKING,
                    null,
                    $comment,
                    $tenderNegotiationId
                );

                return ['success' => true];
            });

        } catch (\Throwable $e) {
            throw $e;
        }
    }

    /**
     * Persist item-wise selections (award=1, is_awarded=0). Used by update and awardItemWiseSupplier.
     */
    public function persistItemWiseSelections(int $tenderId, int $isNegotiation, array $selections, ?int $userId = null): void
    {
        SrmItemWiseTenderAwarding::clearAwardForTenderNegotiation($tenderId, $isNegotiation);
        $systemPickMap = $this->getItemWiseSystemPickBidMap($tenderId, $isNegotiation);

        foreach ($selections as $sel) {
            $bidFormatDetailId = isset($sel['bid_format_detail_id']) ? (int) $sel['bid_format_detail_id'] : null;
            $boqItemId = isset($sel['boq_item_id']) ? (int) $sel['boq_item_id'] : null;
            $bidId = (int) ($sel['bid_id'] ?? 0);
            $supplierId = (int) ($sel['supplier_id'] ?? 0);
            $bidAmount = $sel['bid_amount'] ?? null;
            if (!$bidId || !$supplierId) {
                continue;
            }
            if ($bidFormatDetailId === 0) {
                $bidFormatDetailId = null;
            }
            if ($boqItemId === 0) {
                $boqItemId = null;
            }
            $key = $boqItemId ? 'boq_' . $boqItemId : 'main_' . $bidFormatDetailId;
            $isSystemPick = isset($systemPickMap[$key]) && (int) $systemPickMap[$key] === $bidId;

            $this->saveItemWiseAwarding(
                $tenderId,
                $bidFormatDetailId,
                $boqItemId,
                $bidId,
                $supplierId,
                $bidAmount,
                $isSystemPick,
                $isNegotiation,
                $userId
            );
        }
    }

    /**
     * Mark all line items for a supplier as awarded. Set final_tender_awarded when all lines awarded.
     *
     * @return array{success: bool, all_awarded: bool}
     */
    public function markSupplierAsAwarded(int $tenderId, int $supplierId, string $comment = ''): array
    {
        $tender = TenderMaster::where('id', $tenderId)->first();
        if (!$tender) {
            throw new \RuntimeException(trans('srm_tender_rfx.tender_not_found'));
        }
        $isNegotiation = self::resolveIsNegotiation($tender);

        SrmItemWiseTenderAwarding::markSupplierLinesAsAwarded($tenderId, $isNegotiation, $supplierId);

        $this->sendRegretEmailsForAwardedLines($tenderId, $isNegotiation, $supplierId);

        $counts = SrmItemWiseTenderAwarding::getAwardedCountsForTender($tenderId, $isNegotiation);
        $allAwarded = $counts['total'] > 0 && $counts['awarded'] >= $counts['total'];

        if ($allAwarded) {
            $commentToSave = $comment ?: ($tender->final_tender_award_comment ?? '');
            TenderMaster::where('id', $tenderId)->update([
                'final_tender_awarded' => 1,
                'final_tender_award_comment' => $commentToSave,
            ]);
            TenderConfirmationService::saveConfirmationDetails(
                (int) $tenderId,
                (int) $tenderId,
                TenderConfirmationDetail::MODULE_AWARDED,
                null,
                $commentToSave
            );
        }

        return ['success' => true, 'all_awarded' => $allAwarded];
    }

    /**
     * Get list of awarded suppliers for item-wise tender (for Send Email modal).
     *
     * @return array{tender_code: string, tender_title: string, suppliers: array}
     */
    public function getAwardedSuppliersList(int $tenderId): array
    {
        $tender = TenderMaster::where('id', $tenderId)->first();
        if (!$tender) {
            throw new \RuntimeException(trans('srm_tender_rfx.tender_not_found'));
        }
        $isNegotiation = self::resolveIsNegotiation($tender);

        $rows = SrmItemWiseTenderAwarding::getAwardedRowsForTender($tenderId, $isNegotiation)
            ->with([
                'supplier' => function ($q) {
                    $q->select('id', 'name', 'email');
                },
                'bid_submission_master' => function ($q) {
                    $q->select('id', 'bidSubmissionCode');
                },
            ])
            ->get();

        $bySupplier = [];
        foreach ($rows as $row) {
            $sid = $row->supplier_id;
            if (!isset($bySupplier[$sid])) {
                $supplier = $row->supplier;
                $bidMaster = $row->bid_submission_master;
                $bySupplier[$sid] = [
                    'supplier_id' => $sid,
                    'bid_id' => $row->bid_id,
                    'bid_submission_code' => $bidMaster ? $bidMaster->bidSubmissionCode : '',
                    'supplier_name' => $supplier ? $supplier->name : '',
                    'email' => $supplier && !empty($supplier->email) ? $supplier->email : '',
                    'award_email_sent' => (int) $row->award_email_sent,
                ];
            } elseif ($row->award_email_sent) {
                $bySupplier[$sid]['award_email_sent'] = 1;
            }
        }

        return [
            'tender_code' => $tender->tender_code,
            'tender_title' => $tender->title,
            'suppliers' => array_values($bySupplier),
        ];
    }

    /**
     * Build email body data and send award email to one supplier. Marks award_email_sent = 1.
     */
    public function sendAwardEmailToSupplier(int $tenderId, int $supplierId): void
    {
        $tender = TenderMaster::where('id', $tenderId)->with(['company', 'currency'])->first();
        if (!$tender) {
            throw new \RuntimeException('Tender not found.');
        }
        $isNegotiation = self::resolveIsNegotiation($tender);

        $rows = SrmItemWiseTenderAwarding::getAwardedRowsForTender($tenderId, $isNegotiation, $supplierId)
            ->with('supplier')
            ->get();

        if ($rows->isEmpty()) {
            throw new \RuntimeException(trans('srm_tender_rfx.item_wise_no_awarded_items_for_supplier'));
        }
        $supplier = $rows->first()->supplier;
        if (!$supplier) {
            throw new \RuntimeException(trans('srm_tender_rfx.item_wise_supplier_not_found'));
        }
        $email = $supplier->email ?? SupplierRegistrationLink::where('id', $supplierId)->value('email');
        if (!$email) {
            throw new \RuntimeException(trans('srm_tender_rfx.item_wise_supplier_has_no_email'));
        }

        $currency = $tender->currency ? $tender->currency->CurrencyName : '';
        $companyName = $tender->company ? $tender->company->CompanyName : '';
        $items = $this->buildAwardEmailItems($rows);

        $body = view('email.item_wise_tender_award', [
            'supplierName' => $supplier->name,
            'tenderCode' => $tender->tender_code,
            'tenderTitle' => $tender->title,
            'items' => $items,
            'currency' => $currency,
            'companyName' => $companyName,
        ])->render();

        $dataEmail = [
            'empEmail' => $email,
            'companySystemID' => $tender->company_id,
            'alertMessage' => 'Letter of Awarding | ' . $tender->tender_code . ' | ' . $tender->title,
            'emailAlertMessage' => $body,
            'ccEmail' => [],
            'attachmentList' => [],
        ];
        Email::sendEmailSRM($dataEmail);

        SrmItemWiseTenderAwarding::markAwardEmailSentForSupplier($tenderId, $isNegotiation, $supplierId);
    }

    public function sendRegretEmailsForAwardedLines(
        int $tenderId,
        int $isNegotiation,
        int $awardedSupplierId
    ): void {

        $tender = TenderMaster::with('company')->find($tenderId);
        if (!$tender || (int) $tender->document_type !== 0) {
            return;
        }

        $awardedRows = SrmItemWiseTenderAwarding::getAwardedRowsForSupplier($tenderId, $isNegotiation, $awardedSupplierId);

        if ($awardedRows->isEmpty()) {
            return;
        }

        $lineKeys = [];
        $mainDetailIds = [];
        $boqItemIds = [];
        foreach ($awardedRows as $row) {
            $key = $row->boq_item_id
                ? 'boq_' . $row->boq_item_id
                : 'main_' . $row->bid_format_detail_id;
            $lineKeys[$key] = [
                'bid_format_detail_id' => $row->bid_format_detail_id,
                'boq_item_id' => $row->boq_item_id,
            ];
            if ($row->boq_item_id) {
                $boqItemIds[] = $row->boq_item_id;
            } else {
                $mainDetailIds[] = $row->bid_format_detail_id;
            }
        }

        $supplierToLines = [];
        
        if (!empty($mainDetailIds)) {
            $mainBids = BidMainWork::getBidsByTenderAndDetailIds($tenderId, $mainDetailIds);
            $bidIds = $mainBids->pluck('bid_master_id')->filter()->unique()->values()->all();
            $bidToSupplier = BidSubmissionMaster::getSupplierIdsByTenderAndBidIds($tenderId, $bidIds);
            foreach ($mainBids as $b) {
                $sid = (int) ($b->supplier_registration_id ?: ($bidToSupplier[$b->bid_master_id] ?? 0));
                if ($sid === 0 || $sid === $awardedSupplierId) {
                    continue;
                }
                $key = 'main_' . $b->bid_format_detail_id;
                if (isset($lineKeys[$key])) {
                    $supplierToLines[$sid][$key] = true;
                }
            }
        }

        if (!empty($boqItemIds)) {
            $boqBids = BidBoq::getBidsByBoqIds($boqItemIds);
            $boqBidIds = $boqBids->pluck('bid_master_id')->filter()->unique()->values()->all();
            $boqBidToSupplier = BidSubmissionMaster::getSupplierIdsByTenderAndBidIds($tenderId, $boqBidIds);
            foreach ($boqBids as $b) {
                $sid = (int) ($b->supplier_registration_id ?: ($boqBidToSupplier[$b->bid_master_id] ?? 0));
                if ($sid === 0 || $sid === $awardedSupplierId) {
                    continue;
                }
                $key = 'boq_' . $b->boq_id;
                if (isset($lineKeys[$key])) {
                    $supplierToLines[$sid][$key] = true;
                }
            }
        }

        if (empty($supplierToLines)) {
            return;
        }

        $supplierIds = array_keys($supplierToLines);
        $suppliers = SupplierRegistrationLink::getByIdsKeyed($supplierIds);

        $boqIdsArray = collect($lineKeys)->pluck('boq_item_id')->filter()->unique()->values()->all();
        $boqs = TenderBoqItems::getByIdsKeyed($boqIdsArray);

        $detailIdsArray = collect($lineKeys)->pluck('bid_format_detail_id')->unique()->values()->all();
        $details = PricingScheduleDetail::getByIdsKeyed($detailIdsArray);

        foreach ($supplierToLines as $supplierId => $lineMap) {

            $supplier = $suppliers[$supplierId] ?? null;

            if (!$supplier || empty($supplier->email)) {
                continue;
            }

            $itemLabels = [];

            foreach (array_keys($lineMap) as $key) {

                $line = $lineKeys[$key];

                if (!empty($line['boq_item_id'])) {
                    $label = $boqs[$line['boq_item_id']]->item_name ?? '';
                } else {
                    $label = $details[$line['bid_format_detail_id']]->label ?? '';
                }

                if ($label !== '') {
                    $itemLabels[] = $label;
                }
            }

            if (empty($itemLabels)) {
                continue;
            }

            $itemsList = implode(', ', $itemLabels);

            $body = $this->buildRegretEmailBody(
                $supplier->name,
                $itemsList,
                $tender->company_id,
                $tender->tender_code ?? '',
                $tender->title ?? ''
            );

            $dataEmail = [
                'empEmail' => $supplier->email,
                'companySystemID' => $tender->company_id,
                'alertMessage' => 'Tender Regret',
                'emailAlertMessage' => $body,
                'attachmentList' => [],
                'ccEmail' => [],
            ];

            Email::sendEmailErp($dataEmail);
        }
    }
    private function buildRegretEmailBody(
        string $name,
        string $itemsList,
        int $companyId,
        string $tenderCode = '',
        string $tenderTitle = ''
    ): string {
        $tenderRef = '';
        if ($tenderCode !== '' || $tenderTitle !== '') {
            $tenderRef = '<p><strong>Tender Code:</strong> ' . ($tenderCode ?: 'N/A') . '</p>';
            $tenderRef .= '<p><strong>Tender Title:</strong> ' . ($tenderTitle ?: 'N/A') . '</p><br>';
        }

        $body = "
            Hi {$name}, <br><br>
    
            Thank you for your participation in our tender process.
            We appreciate the effort and time you invested in your proposal.
    
            <br><br>
            {$tenderRef}
            After careful consideration, we regret to inform you that the following item(s)
            have been awarded to another supplier:
    
            <br><br>
    
            <strong>{$itemsList}</strong>
    
            <br><br>
    
            We received several competitive proposals, making our decision a challenging one.
            We hope for future opportunities to collaborate.
    
            <br><br>
    
            Thank you once again for your interest in working with us.
            <br><br>
        ";

        $body .= Helper::getSupplierEmailFooter($companyId);

        return $body;
    }
    private function getDocumentTypeLabel(int $documentType): string
    {
        switch ($documentType) {
            case 1: return 'Quotation';
            case 2: return 'Information';
            case 3: return 'Proposal';
            default: return 'Tender';
        }
    }

    /**
     * Build items array for award email (description, quantity, price per row).
     */
    protected function buildAwardEmailItems($rows): array
    {
        $items = [];
        foreach ($rows as $row) {
            $itemLabel = '';
            if ($row->boq_item_id) {
                $boq = TenderBoqItems::find($row->boq_item_id);
                $itemLabel = $boq ? $boq->item_name : '';
            } else {
                $detail = PricingScheduleDetail::find($row->bid_format_detail_id);
                $itemLabel = $detail ? $detail->label : '';
            }
            $qty = isset($row->quantity) ? $row->quantity : '-';
            $price = $row->bid_amount !== null ? number_format((float) $row->bid_amount, 3) : '-';
            $items[] = ['description' => $itemLabel, 'quantity' => $qty, 'price' => $price];
        }
        return $items;
    }
    public static function saveItemWiseAwarding($tenderId, $bidFormatDetailId, $boqItemId, $bidId, $supplierId,
        $bidAmount, $isSystemPick, $isNegotiation, $userId
    ) {
        return SrmItemWiseTenderAwarding::createOrUpdateAwarding(
            [
                'tender_id' => $tenderId,
                'bid_format_detail_id' => $bidFormatDetailId,
                'boq_item_id' => $boqItemId,
                'bid_id' => $bidId,
            ],
            [
                'supplier_id' => $supplierId,
                'bid_amount' => $bidAmount,
                'award' => 1,
                'is_awarded' => 0,
                'system_pick' => $isSystemPick ? 1 : 0,
                'is_negotiation' => $isNegotiation,
                'updated_by' => $userId,
            ]
        );
    }

    private function normalizeToArray($data): array
    {
        if (empty($data)) {
            return [];
        }

        if (is_array($data)) {
            return $data;
        }

        if (method_exists($data, 'toArray')) {
            return $data->toArray();
        }

        return [];
    }
}
