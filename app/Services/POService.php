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
        $output = ProcumentOrder::select('purchaseOrderID', 'documentSystemID', 'poType_N', 'rcmActivated',
            'purchaseOrderCode', 'createdDateTime', 'referenceNumber', 'projectID', 'soldToAddressDescriprion',
            'soldTocontactPersonID', 'soldTocontactPersonTelephone', 'soldTocontactPersonFaxNo',
            'soldTocontactPersonEmail', 'supplierPrimaryCode', 'supplierName', 'supplierAddress', 'supplierVATEligible',
            'shippingAddressDescriprion', 'shipTocontactPersonID', 'shipTocontactPersonTelephone',
            'shipTocontactPersonFaxNo', 'shipTocontactPersonEmail', 'invoiceToAddressDescription',
            'invoiceTocontactPersonID', 'invoiceTocontactPersonTelephone', 'invoiceTocontactPersonFaxNo',
            'invoiceTocontactPersonEmail', 'narration', 'expectedDeliveryDate', 'vatRegisteredYN',
            'rcmActivated', 'poDiscountAmount', 'supplierVATEligible', 'VATAmount',
            'poTotalSupplierTransactionCurrency', 'deliveryTerms', 'panaltyTerms', 'supplierID', 'companySystemID',
            'localCurrencyID', 'companyReportingCurrencyID', 'supplierTransactionCurrencyID', 'documentSystemID',
            'documentSystemID')
            ->where('purchaseOrderID', $purchaseOrderID)
            ->with([
            'detail' => function ($query) {
                $query->with(['unit' => function ($q) {
                    $q->select('UnitID', 'UnitShortCode');
                }
                ])
                    ->select('purchaseOrderMasterID', 'unitOfMeasure', 'netAmount', 'itemPrimaryCode', 'VATAmount',
                        'supplierPartNumber', 'noQty', 'altUnitValue', 'unitCost', 'discountAmount', 'itemDescription');
            },
            'supplier' => function ($query) {
                $query->select('vatNumber', 'supplierCodeSystem');
            },
            'approved' => function ($query) {
                $query->with(['employee' => function ($q) {
                    $q->select('employeeSystemID', 'empFullName');
                }
                ])
                    ->select('documentSystemCode', 'employeeSystemID', 'approvedDate')
                    ->where('rejectedYN', 0)
                    ->whereIN('documentSystemID', [2]);
            },
            'suppliercontact' => function ($query)
            {
                $query->select('supplierID', 'contactPersonName', 'contactPersonTelephone', 'contactPersonFax',
                    'contactPersonEmail')
                    ->where('isDefault', -1);
            },
            'paymentTerms_by' => function ($query) {
                $query->with('type')
                    ->select('poID', 'LCPaymentYN', 'paymentTemDes', 'comAmount', 'comPercentage', 'inDays', 'comDate');
            },
            'advance_detail' => function ($query) {
                $query->select('poID', 'reqAmount', 'reqAmountInPOLocalCur', 'reqAmountInPORptCur')
                    ->where('poTermID', 0)
                    ->where('confirmedYN', 1)
                    ->where('isAdvancePaymentYN', 1)
                    ->where('approvedYN', -1);
            },
            'company' => function ($query)
            {
                $query->select('companySystemID', 'logoPath', 'CompanyName', 'vatRegisteredYN',
                    'vatRegistratonNumber', 'masterCompanySystemIDReorting');
            },
            'secondarycompany' => function ($query) use ($createdDateTime) {
                $query->select('companySystemID', 'logoPath', 'name')
                    ->whereDate('cutOffDate', '<=', $createdDateTime);
            },
            'transactioncurrency' => function ($query)
            {
                $query->select('currencyID', 'CurrencyCode', 'DecimalPlaces');
            },
            'localcurrency' => function ($query)
            {
                $query->select('currencyID', 'CurrencyCode', 'DecimalPlaces');
            },
            'reportingcurrency' => function ($query)
            {
                $query->select('currencyID', 'CurrencyCode', 'DecimalPlaces');
            },
            'companydocumentattachment' => function ($query)
            {
                $query->select('documentSystemID', 'companySystemID', 'docRefNumber');
            },
            'project' => function ($query)
            {
                $query->select('id', 'description');
            }
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
        $data = $slot->getSlotData([$tenantID], 1);
        return $data;
    }

    public function getPurchaseOrders($wareHouseID, $supplierID, $tenantID, $searchText)
    {
        $searchText = str_replace("\\", "\\\\", $searchText);
        return ProcumentOrder::with([
            'detail' => function ($query) use($searchText){
            $query->select('purchaseOrderMasterID')
                ->where('goodsRecievedYN', '!=', 2);
            $query->when(!empty($searchText), function ($query) use($searchText){
                $query->where('itemPrimaryCode', 'LIKE', "%{$searchText}%");
                $query->orWhere('itemDescription', 'LIKE', "%{$searchText}%");
            });
        }])
            ->whereHas('detail', function ($q) use($searchText){
            $q->where('goodsRecievedYN', '!=', 2);
            $q->when(!empty($searchText), function ($q) use($searchText){
                $q->where('itemPrimaryCode', 'LIKE', "%{$searchText}%");
                $q->orWhere('itemDescription', 'LIKE', "%{$searchText}%");
            });
            })
            ->select('purchaseOrderID', 'purchaseOrderCode')
            ->where('companySystemID', $tenantID)
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
