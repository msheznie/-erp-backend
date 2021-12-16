<?php

namespace App\Services;

use App\Models\ErpItemLedger;
use App\Models\PoAddons;
use App\Models\ProcumentOrder;
use App\Models\SlotMaster;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use function Clue\StreamFilter\fun;

class POService
{

    public function __construct()
    {
    }

    /**
     * get getPoPrintData
     * @return array
     */

    public function getPoPrintData($purchaseOrderID)
    {
        $poBasicData = ProcumentOrder::find($purchaseOrderID);
        $createdDateTime = ($poBasicData) ? Carbon::parse($poBasicData->createdDateTime) : null;
        $output = ProcumentOrder::where('purchaseOrderID', $purchaseOrderID)->with([
            'detail' => function ($query) {
                $query->with('unit');
            }, 'supplier' => function ($query) {
                $query->select('vatNumber', 'supplierCodeSystem');
            }, 'approved' => function ($query) {
                $query->with('employee');
                $query->where('rejectedYN', 0);
                $query->whereIN('documentSystemID', [2]);
            }, 'suppliercontact' => function ($query) {
                $query->where('isDefault', -1);
            }, 'paymentTerms_by' => function ($query) {
                $query->with('type');
            }, 'advance_detail' => function ($query) {
                $query->with(['category_by', 'grv_by', 'currency', 'supplier_by'])
                    ->where('poTermID', 0)
                    ->where('confirmedYN', 1)
                    ->where('isAdvancePaymentYN', 1)
                    ->where('approvedYN', -1);
            }, 'company',
            'secondarycompany' => function ($query) use ($createdDateTime) {
                $query->whereDate('cutOffDate', '<=', $createdDateTime);
            }, 'transactioncurrency', 'localcurrency', 'reportingcurrency', 'companydocumentattachment', 'project'
        ])->first();

        if (!empty($output)) {

            foreach ($output->detail as $item) {

                $date = $output->createdDateTime;

                $item->inhand = ErpItemLedger::where('itemSystemCode', $item->itemCode)
                    ->where('companySystemID', $item->companySystemID)
                    ->sum('inOutQty');

                $dt = new Carbon($date);
                $from = $dt->subMonths(3);;
                $to = new Carbon($date);

                $item->lastThreeMonthIssued = (ErpItemLedger::where('itemSystemCode', $item->itemCode)
                        ->where('companySystemID', $item->companySystemID)
                        ->where('documentSystemID', 8)
                        ->whereBetween('transactionDate', [$from, $to])
                        ->sum('inOutQty')) * -1;
            }
        }
        return $output;
    }

    public function getPoAddons($purchaseOrderID)
    {
        $orderAddons = PoAddons::where('poId', $purchaseOrderID)
            ->with(['category'])
            ->orderBy('idpoAddons', 'DESC')
            ->get();
        return $orderAddons;
    }

    public function getAppointmentSlots($tenantID)
    {
        $slot = new SlotMaster();
        $data = $slot->getSlotData($tenantID);
        return $data;
    }

    public function getPurchaseOrders($wareHouseID, $supplierID, $tenantID)
    {
        return ProcumentOrder::with(['detail.appointmentDetails' => function ($query) {
            $query->whereHas('appointment', function ($q){
                $q->where('refferedBackYN', '!=', -1);
            });
        }, 'detail.unit', 'detail' => function ($query) {
            $query->where('goodsRecievedYN', '!=', 2);
        }])
            ->select('purchaseOrderID', 'purchaseOrderCode')
            ->where('approved', -1)
            ->where('poConfirmedYN', 1)
            ->where('poCancelledYN', 0)
            ->where('poClosedYN', 0)
            ->where('grvRecieved', "<>", 2)
            ->where('WO_confirmedYN', 1)
            ->where('manuallyClosed', 0)
            ->where('poType_N', '<>', 5)
            ->where('supplierID', $supplierID)
            ->orderBy('purchaseOrderID', 'desc')
            ->get();
    }
}
