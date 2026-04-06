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
        return BidSubmissionMaster::getCommercialBidIds($tenderId, $isNegotiation);
    }

    public function getPricingItems($bidMasterId, $tenderId)
    {
        return PricingScheduleMaster::getPricingItemsWithRelations($bidMasterId, $tenderId);
    }
}
