<?php

namespace App\Services;

use App\Models\BidMainWork;
use App\Models\BidSubmissionMaster;
use App\Models\PricingScheduleMaster;
use App\Models\TenderMaster;
use App\Models\TenderNegotiation;

class TenderCommercialBidService
{
    public function getCommercialBids($tenderId, $isNegotiation)
    {
        $tender = TenderMaster::select('id')->withCount([
            'criteriaDetails',
            'criteriaDetails AS go_no_go_count' => function ($query) {
                $query->where('critera_type_id', 1);
            },
            'criteriaDetails AS technical_count' => function ($query) {
                $query->where('critera_type_id', 2);
            }
        ])->withCount(['DocumentAttachments' => function ($q) {
            $q->where('envelopType', 3);
        }])->where('id', $tenderId)->first();

        if (!$tender) {
            return [];
        }

        $tenderBidNegotiations = TenderNegotiation::tenderBidNegotiationList($tenderId, $isNegotiation);

        if ($tenderBidNegotiations->count() > 0) {
            $bidSubmissionMasterIds = $tenderBidNegotiations->pluck('bid_submission_master_id_new')->toArray();
        } else {
            $bidSubmissionMasterIds = [];
        }

        if ($tender->technical_count == 0) {
            $query = BidSubmissionMaster::selectRaw("'' as weightage,srm_bid_submission_master.id,srm_bid_submission_master.bidSubmittedDatetime,srm_bid_submission_master.tender_id,srm_supplier_registration_link.name,'' as bid_id,srm_bid_submission_master.commercial_verify_status,srm_bid_submission_master.bidSubmissionCode,srm_tender_master.technical_passing_weightage as passing_weightage")
                ->join('srm_supplier_registration_link', 'srm_supplier_registration_link.id', '=', 'srm_bid_submission_master.supplier_registration_id')
                ->join('srm_tender_master', 'srm_tender_master.id', '=', 'srm_bid_submission_master.tender_id')
                ->groupBy('srm_bid_submission_master.id')->where('srm_bid_submission_master.status', 1)
                ->where('srm_bid_submission_master.bidSubmittedYN', 1)
                ->where('srm_bid_submission_master.tender_id', $tenderId);

            if ($isNegotiation == 1) {
                $query = $query->whereIn('srm_bid_submission_master.id', $bidSubmissionMasterIds);
            } else {
                $query = $query->whereNotIn('srm_bid_submission_master.id', $bidSubmissionMasterIds);
            }

            return $query->where('srm_bid_submission_master.doc_verifiy_status', 1)->pluck('id');
        }

        $query = BidSubmissionMaster::selectRaw("round(SUM((srm_bid_submission_detail.eval_result/100)*srm_tender_master.technical_weightage),3) as weightage,srm_bid_submission_master.id,srm_bid_submission_master.bidSubmittedDatetime,srm_bid_submission_master.tender_id,srm_bid_submission_detail.id as bid_id,srm_bid_submission_master.commercial_verify_status,srm_bid_submission_master.bidSubmissionCode,srm_tender_master.technical_passing_weightage as passing_weightage")
            ->join('srm_tender_master', 'srm_tender_master.id', '=', 'srm_bid_submission_master.tender_id')
            ->join('srm_bid_submission_detail', 'srm_bid_submission_detail.bid_master_id', '=', 'srm_bid_submission_master.id')
            ->havingRaw('weightage >= passing_weightage')
            ->groupBy('srm_bid_submission_master.id')
            ->where('srm_bid_submission_master.status', 1)->where('srm_bid_submission_master.bidSubmittedYN', 1)->where('srm_bid_submission_master.tender_id', $tenderId)->where('srm_bid_submission_master.commercial_verify_status', 1);

        if ($isNegotiation == 1) {
            $query = $query->whereIn('srm_bid_submission_master.id', $bidSubmissionMasterIds);
        } else {
            $query = $query->whereNotIn('srm_bid_submission_master.id', $bidSubmissionMasterIds);
        }

        return $query->orderBy('srm_bid_submission_master.id', 'asc')->pluck('id');
    }

    public function getPricingItems($bidMasterId, $tenderId)
    {
        if (empty($bidMasterId) || !is_array($bidMasterId)) {
            $bidMasterId = $bidMasterId && method_exists($bidMasterId, 'toArray') ? $bidMasterId->toArray() : [];
        }
        if (!empty($bidMasterId)) {
            BidMainWork::deleteIncompleteBidMainWorkRecords($tenderId, $bidMasterId);
        }

        return PricingScheduleMaster::with(['tender_bid_format_master', 'pricing_shedule_details' => function ($q) use ($bidMasterId) {
            $q->with(['bid_main_works' => function ($q) use ($bidMasterId) {
                $q->whereIn('bid_master_id', $bidMasterId);
            }, 'bid_format_detail' => function ($q) use ($bidMasterId) {
                $q->whereIn('bid_master_id', $bidMasterId);
                $q->orWhere('bid_master_id', null);
            }, 'tender_boq_items' => function ($q) use ($bidMasterId) {
                $q->with(['bid_boqs' => function ($q2) use ($bidMasterId) {
                    $q2->whereIn('bid_master_id', $bidMasterId);
                }, 'ranking_items']);
            }, 'ranking_items'])->whereNotIn('field_type', [4]);
        }])->where('tender_id', $tenderId)->get();
    }
}
