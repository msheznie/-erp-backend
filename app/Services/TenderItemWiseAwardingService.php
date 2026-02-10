<?php

namespace App\Services;

use App\helper\email as Email;
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
        $tenderId = $request['tenderMasterId'];
        $isNegotiation = (int) ($request['isNegotiation'] ?? 0);
        $comment = $request['comment'] ?? '';
        $selections = $request['selections'] ?? [];
        $userId = $request->user()->id ?? null;

        DB::transaction(function () use ($tenderId, $isNegotiation, $comment, $selections, $userId) {
            $bidMasterId = $this->commercialBidService->getCommercialBids($tenderId, $isNegotiation);
            if (empty($bidMasterId) || !is_array($bidMasterId)) {
                $bidMasterId = $bidMasterId && method_exists($bidMasterId, 'toArray') ? $bidMasterId->toArray() : [];
            }

            SrmItemWiseTenderAwarding::clearAwardForTenderNegotiation($tenderId, $isNegotiation);

            $systemPickMap = $this->getItemWiseSystemPickBidMap($tenderId, $isNegotiation);

            foreach ($selections as $sel) {
                $bidFormatDetailId = isset($sel['bid_format_detail_id']) ? (int) $sel['bid_format_detail_id'] : null;
                $boqItemId = isset($sel['boq_item_id']) ? (int) $sel['boq_item_id'] : null;
                $bidId = (int) ($sel['bid_id'] ?? 0);
                $supplierId = (int) ($sel['supplier_id'] ?? 0);
                $bidAmount = isset($sel['bid_amount']) ? $sel['bid_amount'] : null;
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

                SrmItemWiseTenderAwarding::createOrUpdateAwarding(
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

            TenderMaster::updateCombinedRankingStatus($tenderId, $isNegotiation, $comment);

            $tenderNegotiationId = null;
            if ($isNegotiation === 1) {
                $latestNegotiation = TenderNegotiation::getTenderLatestNegotiations($tenderId);
                $tenderNegotiationId = $latestNegotiation ? $latestNegotiation->id : null;
            }
            TenderConfirmationService::saveConfirmationDetails(
                $tenderId,
                $tenderId,
                TenderConfirmationDetail::MODULE_COMBINED_RANKING,
                null,
                $comment,
                $tenderNegotiationId
            );
        });

        return ['success' => true];
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

            SrmItemWiseTenderAwarding::createOrUpdateAwarding(
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
}
