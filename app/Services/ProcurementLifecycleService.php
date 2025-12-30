<?php

namespace App\Services;

use App\Models\PurchaseRequest;
use App\Models\TenderMaster;
use App\Models\ProcumentOrder;
use App\Models\ContractMaster;
use App\Models\TenderPurchaseRequest;
use App\Models\SrmTenderPo;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProcurementLifecycleService
{
    private $prTypeLabels = [
        1 => 'Single Source',
        2 => 'Closed Source',
        3 => 'Open Source',
        4 => 'Negotiated',
    ];

    private const CONTRACT_STATUS_LABELS = [
        1 => 'Amendment',
        2 => 'Addended',
        3 => 'Renewal',
        4 => 'Extension',
        5 => 'Revised',
        6 => 'Termination',
    ];

    /**
     * Get procurement lifecycle report data
     *
     */
    public function getReportData($companyId, $filters = [])
    {
        $documentType = $filters['documentType'] ?? null;
        $dateFrom = $filters['dateFrom'] ?? null;
        $dateTo = $filters['dateTo'] ?? null;

        $isDefaultFilter = empty($documentType) && empty($dateFrom) && empty($dateTo);
        if ($isDefaultFilter) {
            $dateFrom = Carbon::now()->subMonth()->startOfDay()->format('Y-m-d H:i:s');
            $dateTo = Carbon::now()->endOfDay()->format('Y-m-d H:i:s');
        }

        $linkedIds = $this->getLinkedDocumentIds($companyId);
        $results = collect();
        $processFlags = $this->getProcessFlags($documentType);

        if ($documentType === 'PO' && ($dateFrom || $dateTo)) {
            $this->processPRsWithPODateFilter($companyId, $results, $dateFrom, $dateTo);
        }
        
        // Process PRs with optimized queries
        if ($processFlags['processPR']) {
            $this->processPRs($companyId, $results, $dateFrom, $dateTo, $documentType);
        }

        if ($processFlags['processTender'] || $processFlags['processRFX']) {
            $this->processStandaloneTenders(
                $companyId, 
                $linkedIds['linkedTenderIds'], 
                $results, 
                $dateFrom, 
                $dateTo, 
                $documentType
            );
        }

        if ($processFlags['processPO']) {
            $this->processStandalonePOs(
                $companyId, 
                $linkedIds['linkedPOIds'], 
                $results, 
                $dateFrom, 
                $dateTo, 
                $documentType
            );
        }

        if ($processFlags['processContract']) {
            $this->processStandaloneContracts(
                $companyId, 
                $linkedIds['linkedContractIds'], 
                $results, 
                $dateFrom, 
                $dateTo, 
                $documentType
            );
        }

        return $this->removeDuplicatesAndSort($results);
    }
    /**
     * Get linked document IDs to avoid N+1 queries
     *
     */
    private function getLinkedDocumentIds($companyId)
    {
        $linkedTenderIds = TenderPurchaseRequest::getLinkedTenderIds($companyId);
        
        $linkedPOIds = PurchaseRequest::getLinkedPOIds($companyId);
        
        $tenderPOIds = SrmTenderPo::getActiveTenderPOIds($companyId);
        
        $linkedPOIds = array_unique(array_merge($linkedPOIds, $tenderPOIds));
        
        $linkedContractIds = TenderMaster::getLinkedContractIds($companyId);

        return [
            'linkedTenderIds' => $linkedTenderIds,
            'linkedPOIds' => $linkedPOIds,
            'linkedContractIds' => $linkedContractIds,
        ];
    }

    /**
     * Get process flags based on document type filter
     */
    private function getProcessFlags($documentType)
    {
        return [
            'processPR' => !$documentType || $documentType === 'PR',
            'processTender' => !$documentType || $documentType === 'Tender',
            'processRFX' => !$documentType || $documentType === 'RFX',
            'processPO' => !$documentType || $documentType === 'PO',
            'processContract' => !$documentType || $documentType === 'Contract',
        ];
    }

    /**
     * Process PRs that have POs created in the specified date range
     */
    private function processPRsWithPODateFilter($companyId, &$results, $dateFrom = null, $dateTo = null)
    {
        $prIds = PurchaseRequest::getPRsWithPOCreatedInDateRange($companyId, $dateFrom, $dateTo);
        
        if (!empty($prIds)) {
            PurchaseRequest::whereIn('purchaseRequestID', $prIds)
                ->select('purchaseRequestID', 'purchaseRequestCode', 'prType', 'currency', 'createdDateTime')
                ->orderBy('createdDateTime', 'desc')
                ->chunk(100, function ($prs) use ($companyId, &$results) {
                    $prIds = $prs->pluck('purchaseRequestID')->toArray();
                    $prData = PurchaseRequest::loadPRRelationshipsForReport($prIds, $companyId);
                    
                    foreach ($prs as $pr) {
                        $prKey = $pr->purchaseRequestID;
                        $results->push($this->formatPRRow($pr, $prData[$prKey] ?? []));
                    }
                });
        }
    }

    /**
     * Process PRs in chunks to avoid memory issues
     */
    private function processPRs($companyId, &$results, $dateFrom = null, $dateTo = null, $documentType = null)
    {
        PurchaseRequest::getConfirmedPRsForReport($companyId, $dateFrom, $dateTo)
            ->chunk(100, function ($prs) use ($companyId, &$results, $documentType) {
                $prIds = $prs->pluck('purchaseRequestID')->toArray();
                $prData = PurchaseRequest::loadPRRelationshipsForReport($prIds, $companyId);
                
                foreach ($prs as $pr) {
                    $prKey = $pr->purchaseRequestID;
                    $row = $this->formatPRRow($pr, $prData[$prKey] ?? []);

                    if ($documentType && $documentType !== 'PR') {
                        if ($this->shouldIncludePRForDocumentType($prData[$prKey] ?? [], $documentType)) {
                            $results->push($row);
                        }
                    } else {
                        $results->push($row);
                    }
                }
            });
    }

    /**
     * Process standalone Tenders
     */
    private function processStandaloneTenders($companyId, $linkedTenderIds, &$results, $dateFrom = null, $dateTo = null, $documentType = null)
    {
        TenderMaster::getStandaloneTendersForReport($companyId, $linkedTenderIds, $dateFrom, $dateTo, $documentType)
            ->chunk(100, function ($tenders) use ($companyId, &$results, $documentType) {
                $tenderIds = $tenders->pluck('id')->toArray();
                $tenderData = TenderMaster::loadTenderRelationshipsForReport($tenderIds, $companyId);
                
                foreach ($tenders as $tender) {
                    $tenderKey = $tender->id;
                    $row = $this->formatTenderRow($tender, $tenderData[$tenderKey] ?? []);

                    if ($documentType && $documentType !== 'Tender' && $documentType !== 'RFX') {
                        if ($this->shouldIncludeTenderForDocumentType($tenderData[$tenderKey] ?? [], $documentType)) {
                            $results->push($row);
                        }
                    } else {
                        $results->push($row);
                    }
                }
            });
    }

    /**
     * Process standalone POs
     */
    private function processStandalonePOs($companyId, $linkedPOIds, &$results, $dateFrom = null, $dateTo = null, $documentType = null)
    {
        ProcumentOrder::getStandalonePOsForReport($companyId, $linkedPOIds, $dateFrom, $dateTo)
            ->chunk(100, function ($pos) use ($companyId, &$results) {
                $poIds = $pos->pluck('purchaseOrderID')->toArray();
                $poData = ProcumentOrder::loadPORelationshipsForReport($poIds, $companyId);
                
                foreach ($pos as $po) {
                    $poKey = $po->purchaseOrderID;
                    $results->push($this->formatPORow($po, $poData[$poKey] ?? []));
                }
            });
    }

    /**
     * Process standalone Contracts
     */
    private function processStandaloneContracts($companyId, $linkedContractIds, &$results, $dateFrom = null, $dateTo = null, $documentType = null)
    {
        ContractMaster::getStandaloneContractsForReport($companyId, $linkedContractIds, $dateFrom, $dateTo)
            ->chunk(100, function ($contracts) use (&$results) {
                $contractIds = $contracts->pluck('id')->toArray();
                $contractData = ContractMaster::loadContractRelationshipsForReport($contractIds);
                
                foreach ($contracts as $contract) {
                    $contractKey = $contract->id;
                    $results->push($this->formatContractRow($contract, $contractData[$contractKey] ?? []));
                }
            });
    }

    /**
     * Check if PR should be included for document type filter
     */
    private function shouldIncludePRForDocumentType($prData, $documentType)
    {
        if ($documentType === 'Tender' || $documentType === 'RFX') {
            return !empty($prData['tenderId']);
        } elseif ($documentType === 'PO') {
            return !empty($prData['poIds']);
        } elseif ($documentType === 'Contract') {
            $tenderData = $prData['tenderData'] ?? [];
            return !empty($tenderData['contractData']);
        }
        return false;
    }

    /**
     * Check if Tender should be included for document type filter
     */
    private function shouldIncludeTenderForDocumentType($tenderData, $documentType)
    {
        if ($documentType === 'PO') {
            return !empty($tenderData['poId']);
        } elseif ($documentType === 'Contract') {
            return !empty($tenderData['contractData']);
        }
        return false;
    }

    /**
     * Format PR row
     */
    private function formatPRRow($pr, $prData)
    {
        $currencyCode = $prData['currencyCode'] ?? '';
        $decimalPlace = $prData['decimalPlace'] ?? 2;
        $total = $prData['total'] ?? 0;
        $prValue = $currencyCode ? ($currencyCode . ' ' . number_format($total, (int) $decimalPlace)) : '-';

        $prApprovalLevel = $this->processApprovalsFromRaw($prData['approvals'] ?? collect());

        // Format PO data
        $poData = [];
        foreach ($prData['poData'] ?? [] as $poId => $poInfo) {
            $poData[] = [
                'poCode' => $poInfo['poCode'] ?? '',
                'poApprovals' => $this->processApprovalsFromRaw($poInfo['approvals'] ?? collect())
            ];
        }

        $tenderData = $prData['tenderData'] ?? [];
        $tender = $tenderData['tender'] ?? null;
        $tenderCode = $tender ? ($tender->tender_code ?? '-') : '-';
        $tenderApprovals = $this->processApprovalsFromRaw($tenderData['approvals'] ?? collect());
        $bidSubmissionDate = $this->formatDateTime($tender->bid_submission_opening_date);
        $technicalEvaluationDate = $this->formatDateTime($tender->technical_bid_opening_date);
        $commercialEvaluationDate = $this->formatDateTime($tender->commerical_bid_opening_date);
        $publishedDate = $this->formatDateTime($tender->published_at);

        if (isset($tenderData['poId']) && $tenderData['poId'] && !empty($tenderData['poData'])) {
            $tenderPO = $tenderData['poData'];
            $poData[] = [
                'poCode' => $tenderPO['poCode'] ?? '',
                'poApprovals' => $this->processApprovalsFromRaw($tenderPO['approvals'] ?? collect())
            ];
        }

        $contractData = $tenderData['contractData'] ?? [];
        $contract = $contractData['contract'] ?? null;
        $contractCode = $contract ? ($contract->contractCode ?? '-') : '-';
        $contractVariation = 'no';
        $contractVariationTypes = [];
        $commencementDate = '-';
        $agreementSignedDate = '-';
        $contractEndDate = '-';

        if ($contract) {
            $commencementDate = $this->formatDateOnly($contract->startDate);
            $agreementSignedDate = $this->formatDateOnly($contract->agreementSignDate);
            $contractEndDate = $this->formatDateOnly($contract->endDate);
            [$contractVariation, $contractVariationTypes] = $this->getContractVariation($contractData);
        }

        return [
            'prCode' => $pr->purchaseRequestCode,
            'prValue' => $prValue,
            'prType' => $this->prTypeLabels[$pr->prType] ?? '-',
            'prApprovalLevel' => $prApprovalLevel,
            'poData' => $poData,
            'tenderCode' => $tenderCode,
            'tenderApprovals' => $tenderApprovals,
            'bidSubmissionDate' => $bidSubmissionDate,
            'technicalEvaluationDate' => $technicalEvaluationDate,
            'commercialEvaluationDate' => $commercialEvaluationDate,
            'publishedDate' => $publishedDate,
            'contractCode' => $contractCode,
            'contractVariation' => $contractVariation,
            'contractVariationTypes' => $contractVariationTypes,
            'commencementDate' => $commencementDate,
            'agreementSignedDate' => $agreementSignedDate,
            'contractEndDate' => $contractEndDate,
            'prCreatedDate' => $pr->createdDateTime ? Carbon::parse($pr->createdDateTime)->format('Y-m-d H:i:s') : null,
        ];
    }

    /**
     * Format Tender row
     */
    private function formatTenderRow($tender, $tenderData)
    {
        $tenderInfo = $tenderData['tender'] ?? $tender;
        $tenderCode = $tenderInfo->tender_code ?? '-';
        $tenderApprovals = $this->processApprovalsFromRaw($tenderData['approvals'] ?? collect());
        $bidSubmissionDate = $this->formatDateTime($tenderInfo->bid_submission_opening_date);
        $technicalEvaluationDate = $this->formatDateTime($tenderInfo->technical_bid_opening_date);
        $commercialEvaluationDate = $this->formatDateTime($tenderInfo->commerical_bid_opening_date);
        $publishedDate = $this->formatDateTime($tenderInfo->published_at);

        // Get PO from Tender if exists
        $poData = [];
        if (isset($tenderData['poId']) && $tenderData['poId'] && isset($tenderData['poData']) && !empty($tenderData['poData'])) {
            $tenderPO = $tenderData['poData'];
            $poData[] = [
                'poCode' => $tenderPO['poCode'] ?? '',
                'poApprovals' => $this->processApprovalsFromRaw($tenderPO['approvals'] ?? collect())
            ];
        }

        // Get Contract data
        $contractData = $tenderData['contractData'] ?? [];
        $contract = $contractData['contract'] ?? null;
        $contractCode = $contract ? ($contract->contractCode ?? '-') : '-';
        $contractVariation = 'no';
        $contractVariationTypes = [];
        $commencementDate = '-';
        $agreementSignedDate = '-';
        $contractEndDate = '-';

        if ($contract) {
            $commencementDate = $this->formatDateOnly($contract->startDate);
            $agreementSignedDate = $this->formatDateOnly($contract->agreementSignDate);
            $contractEndDate = $this->formatDateOnly($contract->endDate);
            [$contractVariation, $contractVariationTypes] = $this->getContractVariation($contractData);
        }

        return [
            'prCode' => '-',
            'prValue' => '-',
            'prType' => '-',
            'prApprovalLevel' => [],
            'poData' => $poData,
            'tenderCode' => $tenderCode,
            'tenderApprovals' => $tenderApprovals,
            'bidSubmissionDate' => $bidSubmissionDate,
            'technicalEvaluationDate' => $technicalEvaluationDate,
            'commercialEvaluationDate' => $commercialEvaluationDate,
            'publishedDate' => $publishedDate,
            'contractCode' => $contractCode,
            'contractVariation' => $contractVariation,
            'contractVariationTypes' => $contractVariationTypes,
            'commencementDate' => $commencementDate,
            'agreementSignedDate' => $agreementSignedDate,
            'contractEndDate' => $contractEndDate,
            'tenderCreatedDate' => $tender->created_at ? Carbon::parse($tender->created_at)->format('Y-m-d H:i:s') : null,
        ];
    }

    /**
     * Format PO row
     */
    private function formatPORow($po, $poData)
    {
        $poCode = $poData['poCode'] ?? ($po->purchaseOrderCode ?? '-');
        $poApprovals = $this->processApprovalsFromRaw($poData['approvals'] ?? collect());

        return [
            'prCode' => '-',
            'prValue' => '-',
            'prType' => '-',
            'prApprovalLevel' => [],
            'poData' => [[
                'poCode' => $poCode,
                'poApprovals' => $poApprovals
            ]],
            'tenderCode' => '-',
            'tenderApprovals' => [],
            'bidSubmissionDate' => '-',
            'technicalEvaluationDate' => '-',
            'commercialEvaluationDate' => '-',
            'publishedDate' => '-',
            'contractCode' => '-',
            'contractVariation' => 'no',
            'contractVariationTypes' => [],
            'commencementDate' => '-',
            'agreementSignedDate' => '-',
            'contractEndDate' => '-',
            'poCreatedDate' => $po->createdDateTime ? Carbon::parse($po->createdDateTime)->format('Y-m-d H:i:s') : null,
        ];
    }

    /**
     * Format Contract row
     */
    private function formatContractRow($contract, $contractData)
    {
        $contractInfo = $contractData['contract'] ?? $contract;
        $contractCode = $contractInfo->contractCode ?? '-';
        $commencementDate = $this->formatDateOnly($contractInfo->startDate);
        $agreementSignedDate = $this->formatDateOnly($contractInfo->agreementSignDate);
        $contractEndDate = $this->formatDateOnly($contractInfo->endDate);
        [$contractVariation, $contractVariationTypes] = $this->getContractVariation($contractData);

        return [
            'prCode' => '-',
            'prValue' => '-',
            'prType' => '-',
            'prApprovalLevel' => [],
            'poData' => [],
            'tenderCode' => '-',
            'tenderApprovals' => [],
            'bidSubmissionDate' => '-',
            'technicalEvaluationDate' => '-',
            'commercialEvaluationDate' => '-',
            'publishedDate' => '-',
            'contractCode' => $contractCode,
            'contractVariation' => $contractVariation,
            'contractVariationTypes' => $contractVariationTypes,
            'commencementDate' => $commencementDate,
            'agreementSignedDate' => $agreementSignedDate,
            'contractEndDate' => $contractEndDate,
            'contractCreatedDate' => $contract->created_at ? Carbon::parse($contract->created_at)->format('Y-m-d H:i:s') : null,
        ];
    }

    /**
     * Process approvals from raw query results
     */
    private function processApprovalsFromRaw($approvals)
    {
        if (!$approvals || $approvals->isEmpty()) {
            return [];
        }

        return $approvals
            ->map(function ($approval) {
                $name = $approval->empName ?? null;
                $date = isset($approval->approvedDate) && $approval->approvedDate
                    ? Carbon::parse($approval->approvedDate)->format('d/m/Y, h:i A')
                    : null;

                return $name ? [
                    'level' => $approval->rollLevelOrder ?? 0,
                    'name' => $name,
                    'date' => $date,
                    'formatted' => 'Level ' . ($approval->rollLevelOrder ?? 0) . ': ' . trim($name . ($date ? ", $date" : ''))
                ] : null;
            })
            ->filter()
            ->values()
            ->toArray();
    }

    /**
     * Remove duplicates and sort results
     */
    private function removeDuplicatesAndSort(Collection $results): Collection
    {
        return $results
            ->unique(function ($item) {
                return
                    $item['prCode'] !== '-' ? 'PR_'.$item['prCode'] :
                        ($item['tenderCode'] !== '-' ? 'TENDER_'.$item['tenderCode'] :
                            (!empty($item['poData'][0]['poCode']) ? 'PO_'.$item['poData'][0]['poCode'] :
                                ($item['contractCode'] !== '-' ? 'CONTRACT_'.$item['contractCode'] : uniqid())));
            })
            ->sortByDesc(function ($item) {
                return max(array_filter([
                    $item['prCreatedDate'] ?? null,
                    $item['tenderCreatedDate'] ?? null,
                    $item['poCreatedDate'] ?? null,
                    $item['contractCreatedDate'] ?? null,
                ])) ?? '2025-01-01';
            })
            ->values();
    }
    private function formatDateTime($date, $fallback = '-')
    {
        return $date ? Carbon::parse($date)->format('d/m/Y, h:i A') : $fallback;
    }

    private function formatDateOnly($date, $fallback = '-')
    {
        return $date ? Carbon::parse($date)->format('d/m/Y') : $fallback;
    }

    private function getContractVariation(array $contractData): array
    {
        $statuses = $contractData['statuses'] ?? collect();

        if ($statuses->isEmpty()) {
            return ['no', []];
        }

        $types = $statuses->pluck('status')
            ->map(function ($s) {
                return self::CONTRACT_STATUS_LABELS[$s] ?? null;
            })
            ->filter()
            ->values()
            ->toArray();

        return ['yes', $types];
    }
}

