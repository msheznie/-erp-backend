<?php

namespace App\Repositories;

use App\Models\CustomerInvoice;
use App\Repositories\BaseRepository;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerReceivePayment;
use App\Models\CustomerReceivePaymentDetail;
use App\Models\DocumentSystemMapping;
use App\Models\MatchDocumentMaster;
use App\Models\SalesReturn;
use App\Models\SalesReturnDetail;
use App\Models\Taxdetail;
use App\Models\ThirdPartySystems;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

/**
 * Class CustomerInvoiceRepository
 * @package App\Repositories
 * @version June 8, 2018, 4:41 am UTC
 *
 * @method CustomerInvoice findWithoutFail($id, $columns = ['*'])
 * @method CustomerInvoice find($id, $columns = ['*'])
 * @method CustomerInvoice first($columns = ['*'])
*/
class CustomerInvoiceRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'transactionMode',
        'companyID',
        'documentID',
        'serialNo',
        'companyFinanceYearID',
        'FYBiggin',
        'FYEnd',
        'companyFinancePeriodID',
        'FYPeriodDateFrom',
        'FYPeriodDateTo',
        'serviceLineSystemID',
        'serviceLineCode',
        'wareHouseSystemCode',
        'bookingInvCode',
        'bookingDate',
        'comments',
        'invoiceDueDate',
        'customerGRVAutoID',
        'bankID',
        'bankAccountID',
        'performaDate',
        'wanNO',
        'PONumber',
        'rigNo',
        'customerID',
        'customerGLCode',
        'customerInvoiceNo',
        'customerInvoiceDate',
        'custTransactionCurrencyID',
        'custTransactionCurrencyER',
        'companyReportingCurrencyID',
        'companyReportingER',
        'localCurrencyID',
        'localCurrencyER',
        'bookingAmountTrans',
        'bookingAmountLocal',
        'bookingAmountRpt',
        'confirmedYN',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approved',
        'approvedDate',
        'postedDate',
        'servicePeriod',
        'paymentInDaysForJob',
        'serviceStartDate',
        'serviceEndDate',
        'isPerforma',
        'documentType',
        'secondaryLogoCompID',
        'secondaryLogo',
        'timesReferred',
        'RollLevForApp_curr',
        'selectedForTracking',
        'customerInvoiceTrackingID',
        'interCompanyTransferYN',
        'canceledYN',
        'canceledByEmpID',
        'canceledByEmpName',
        'vatOutputGLCodeSystemID',
        'vatOutputGLCode',
        'VATPercentage',
        'VATAmount',
        'VATAmountLocal',
        'VATAmountRpt',
        'canceledDateTime',
        'canceledComments',
        'createdUserGroup',
        'createdUserID',
        'createdPcID',
        'modifiedUser',
        'modifiedPc',
        'createdDateTime',
        'timestamp',
        'discountLocalAmount',
        'discountAmount',
        'discountRptAmount'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CustomerInvoice::class;
    }


    public function thirdPartyDescriptionsForGeneratedFrom(string $generatedFrom): array
    {
        if ($generatedFrom === 'POS') {
            return ['GPOS', 'RPOS'];
        }
        if ($generatedFrom === 'CLUB') {
            return ['CBM'];
        }

        return [];
    }


    public function generatedFromLabelSubQuery(): QueryBuilder
    {
        $dsmTable = (new DocumentSystemMapping)->getTable();
        $tpsTable = (new ThirdPartySystems)->getTable();

        $latestMappingIdPerDocument = DB::table($dsmTable)
            ->where('documentSystemId', 20)
            ->groupBy('documentId')
            ->selectRaw('documentId, MAX(id) as max_mapping_id');

        return DB::table($dsmTable.' as dsm')
            ->joinSub($latestMappingIdPerDocument, 'latest_map', 'latest_map.max_mapping_id', '=', 'dsm.id')
            ->join($tpsTable.' as tps', 'tps.id', '=', 'dsm.thirdPartySystemId')
            ->select('dsm.documentId as documentId', 'tps.description as generated_from_label');
    }

    public function mappingInvoiceIdsSubQuery(array $generatedFromList): QueryBuilder
    {
  
        $descriptions = array_values(array_unique(array_filter(array_map(function ($value) {
            $value = trim((string) $value);
            return $value === '' ? null : $value;
        }, $generatedFromList))));

        $dsmTable = (new DocumentSystemMapping)->getTable();
        $tpsTable = (new ThirdPartySystems)->getTable();

        if ($descriptions === []) {
            return DB::table($dsmTable.' as dsm')
                ->whereRaw('1 = 0')
                ->select('dsm.documentId');
        }

        return DB::table($dsmTable.' as dsm')
            ->join($tpsTable.' as tps', 'tps.id', '=', 'dsm.thirdPartySystemId')
            ->where('dsm.documentSystemId', 20)
            ->whereIn('tps.description', $descriptions)
            ->select('dsm.documentId')
            ->distinct();
    }

    public function receiptPaymentBaseQuery(array $subCompanies): QueryBuilder
    {
        $detTable = (new CustomerReceivePaymentDetail)->getTable();
        $rvTable = (new CustomerReceivePayment)->getTable();

        return CustomerReceivePaymentDetail::query()
            ->from($detTable.' as det')
            ->join($rvTable.' as rv', 'det.custReceivePaymentAutoID', '=', 'rv.custReceivePaymentAutoID')
            ->whereIn('det.companySystemID', $subCompanies)
            ->where('det.addedDocumentSystemID', 20)
            ->where(function ($qq) {
                $qq->whereNull('det.matchingDocID')->orWhere('det.matchingDocID', 0);
            })
            ->toBase();
    }

    public function matchingPaymentBaseQuery(array $subCompanies): QueryBuilder
    {
        $detTable = (new CustomerReceivePaymentDetail)->getTable();
        $mTable = (new MatchDocumentMaster)->getTable();

        return CustomerReceivePaymentDetail::query()
            ->from($detTable.' as det')
            ->join($mTable.' as m', function ($join) {
                $join->on('m.matchDocumentMasterAutoID', '=', 'det.matchingDocID')
                    ->on('m.companySystemID', '=', 'det.companySystemID');
            })
            ->whereIn('det.companySystemID', $subCompanies)
            ->where('det.addedDocumentSystemID', 20)
            ->where('det.matchingDocID', '>', 0)
            ->toBase();
    }

    public function salesReturnBaseQuery(array $subCompanies): QueryBuilder
    {
        $srdTable = (new SalesReturnDetail)->getTable();
        $srTable = (new SalesReturn)->getTable();

        return SalesReturnDetail::query()
            ->from($srdTable.' as srd')
            ->join($srTable.' as sr', 'srd.salesReturnID', '=', 'sr.id')
            ->whereIn('srd.companySystemID', $subCompanies)
            ->toBase();
    }


    public function approvedBalancesQuery(array $subCompanies, ?array $generatedFromList): EloquentBuilder
    {
        $taxTable = (new Taxdetail)->getTable();
        $invTable = (new CustomerInvoiceDirect)->getTable();

        $mappingInvSub = $generatedFromList !== null
            ? $this->mappingInvoiceIdsSubQuery($generatedFromList)
            : null;

        $receiptPaidSub = $this->receiptPaymentBaseQuery($subCompanies);
        if ($mappingInvSub !== null) {
            $receiptPaidSub->joinSub($mappingInvSub, 'dsm_inv', 'dsm_inv.documentId', '=', 'det.bookingInvCodeSystem');
        }
        $receiptPaidSub = $receiptPaidSub
            ->selectRaw('det.bookingInvCodeSystem as invoice_id, SUM(IFNULL(det.receiveAmountTrans,0)) as receipt_paid')
            ->groupBy('det.bookingInvCodeSystem');

        $matchingPaidSub = $this->matchingPaymentBaseQuery($subCompanies);
        if ($mappingInvSub !== null) {
            $matchingPaidSub->joinSub($mappingInvSub, 'dsm_inv', 'dsm_inv.documentId', '=', 'det.bookingInvCodeSystem');
        }
        $matchingPaidSub = $matchingPaidSub
            ->selectRaw('det.bookingInvCodeSystem as invoice_id, SUM(IFNULL(det.receiveAmountTrans,0)) as matching_paid')
            ->groupBy('det.bookingInvCodeSystem');

        $returnSub = $this->salesReturnBaseQuery($subCompanies);
        if ($mappingInvSub !== null) {
            $returnSub->joinSub($mappingInvSub, 'dsm_inv', 'dsm_inv.documentId', '=', 'srd.custInvoiceDirectAutoID');
        }
        $returnSub = $returnSub
            ->selectRaw('srd.custInvoiceDirectAutoID as invoice_id, SUM(IFNULL(srd.transactionAmount,0) + (IFNULL(srd.transactionAmount,0) * IFNULL(srd.VATPercentage,0) / 100)) as return_trans')
            ->groupBy('srd.custInvoiceDirectAutoID');

        $taxSub = DB::table($taxTable.' as td')
            ->where('td.documentSystemID', 20)
            ->whereIn('td.companySystemID', $subCompanies);
        if ($mappingInvSub !== null) {
            $taxSub->joinSub($mappingInvSub, 'dsm_inv', 'dsm_inv.documentId', '=', 'td.documentSystemCode');
        }
        $taxSub = $taxSub
            ->selectRaw('td.documentSystemCode as invoice_id, SUM(IFNULL(td.amount,0)) as tax_amount')
            ->groupBy('td.documentSystemCode');

        $query = CustomerInvoiceDirect::query()
            ->with(['customer', 'warehouse', 'currency'])
            ->leftJoinSub($receiptPaidSub, 'receipt_paid', 'receipt_paid.invoice_id', '=', $invTable.'.custInvoiceDirectAutoID')
            ->leftJoinSub($matchingPaidSub, 'matching_paid', 'matching_paid.invoice_id', '=', $invTable.'.custInvoiceDirectAutoID')
            ->leftJoinSub($returnSub, 'ret', 'ret.invoice_id', '=', $invTable.'.custInvoiceDirectAutoID')
            ->leftJoinSub($taxSub, 'tax', 'tax.invoice_id', '=', $invTable.'.custInvoiceDirectAutoID')
            ->leftJoinSub($this->generatedFromLabelSubQuery(), 'gen_src', 'gen_src.documentId', '=', $invTable.'.custInvoiceDirectAutoID')
            ->whereIn($invTable.'.companySystemID', $subCompanies)
            ->where($invTable.'.confirmedYN', 1)
            ->where($invTable.'.approved', -1)
            ->where(function ($q) use ($invTable) {
                $q->whereNull($invTable.'.canceledYN')->orWhere($invTable.'.canceledYN', 0);
            })
            ->select($invTable.'.*')
            ->selectRaw('(IFNULL('.$invTable.'.bookingAmountTrans,0) + IFNULL(tax.tax_amount,0)) as invoice_amount')
            ->selectRaw('IFNULL(tax.tax_amount,0) as tax_amount')
            ->selectRaw('(IFNULL(receipt_paid.receipt_paid,0) + IFNULL(matching_paid.matching_paid,0) + IFNULL(ret.return_trans,0)) as paid_amount')
            ->selectRaw('IFNULL(receipt_paid.receipt_paid,0) as receipt_paid_amount')
            ->selectRaw('IFNULL(matching_paid.matching_paid,0) as matching_paid_amount')
            ->selectRaw('IFNULL(ret.return_trans,0) as return_amount')
            ->selectRaw('((IFNULL('.$invTable.'.bookingAmountTrans,0) + IFNULL(tax.tax_amount,0)) - (IFNULL(receipt_paid.receipt_paid,0) + IFNULL(matching_paid.matching_paid,0)) - IFNULL(ret.return_trans,0)) as balance_amount')
            ->selectRaw('CASE
                    WHEN (
                        (IFNULL(' . $invTable . '.bookingAmountTrans,0) + IFNULL(tax.tax_amount,0))
                        -
                        (IFNULL(receipt_paid.receipt_paid,0) + IFNULL(matching_paid.matching_paid,0) + IFNULL(ret.return_trans,0))
                    ) <= 0
                    THEN "Fully paid"
                    WHEN (IFNULL(receipt_paid.receipt_paid,0) + IFNULL(matching_paid.matching_paid,0) + IFNULL(ret.return_trans,0)) > 0 THEN "Partially paid"
                    ELSE "Unpaid"
                END as balance_payment_status')
            ->addSelect(DB::raw('gen_src.generated_from_label as generated_from_display'));

        if ($mappingInvSub !== null) {
            $query->joinSub($mappingInvSub, 'dsm_inv', 'dsm_inv.documentId', '=', $invTable.'.custInvoiceDirectAutoID');
        }

        return $query;
    }

    public function receiptVoucherStatusLabel($rvApproved, $rvConfirmed): string
    {
        $rvConfirmed = (int) ($rvConfirmed ?? 0);
        $rvApproved = (int) ($rvApproved ?? 0);

        if ($rvConfirmed === 0) {
            return 'Unconfirmed';
        }

        if ($rvConfirmed === 1 && $rvApproved === 0) {
            return 'Unapproved';
        }

        if ($rvApproved === -1) {
            return 'Approved';
        }

        return 'Pending';
    }

    public function matchingStatusLabel($matchingConfirmed): string
    {

        if($matchingConfirmed === 0)
        {
            return 'Unconfirmed';
        }
        elseif($matchingConfirmed === 1)
        {
            return 'Confirmed';
        }
        else{
            return 'Pending';
        }
    }

    public function salesReturnStatusLabel($srApproved, $srConfirmed): string
    {
        $srConfirmed = (int) ($srConfirmed ?? 0);
        $srApproved = (int) ($srApproved ?? 0);

        if ($srConfirmed === 0) {
            return 'Unconfirmed';
        }

        if ($srConfirmed === 1 && $srApproved === 0) {
            return 'Unapproved';
        }

        if ($srApproved === -1) {
            return 'Approved';
        }

        return 'Pending';
    }


    public function loadStatusDetailsByInvoiceIds(array $invoiceIds, array $subCompanies): array
    {
        if ($invoiceIds === []) {
            return [];
        }

        $invoiceIds = array_values(array_unique(array_map('intval', $invoiceIds)));
        $byInvoice = array_fill_keys($invoiceIds, []);

        $receiptRows = $this->receiptPaymentBaseQuery($subCompanies)
            ->whereIn('det.bookingInvCodeSystem', $invoiceIds)
            ->groupBy('det.bookingInvCodeSystem', 'det.custReceivePaymentAutoID', 'rv.custPaymentReceiveCode', 'rv.confirmedYN', 'rv.approved')
            ->selectRaw('
                det.bookingInvCodeSystem as invoice_id,
                rv.custPaymentReceiveCode as doc_code,
                SUM(IFNULL(det.receiveAmountTrans,0)) as amount,
                rv.confirmedYN as rv_confirmed,
                rv.approved as rv_approved
            ')
            ->get();

        foreach ($receiptRows as $r) {
            $iid = (int) $r->invoice_id;
            if (! array_key_exists($iid, $byInvoice)) {
                continue;
            }
            $byInvoice[$iid][] = [
                'document_type' => 'receipt',
                'document_code' => $r->doc_code,
                'amount' => (float) ($r->amount ?? 0),
                'document_status' => $this->receiptVoucherStatusLabel($r->rv_approved, $r->rv_confirmed),
            ];
        }

        $matchingRows = $this->matchingPaymentBaseQuery($subCompanies)
            ->whereIn('det.bookingInvCodeSystem', $invoiceIds)
            ->groupBy('det.bookingInvCodeSystem', 'm.matchDocumentMasterAutoID', 'm.matchingDocCode', 'm.matchingConfirmedYN')
            ->selectRaw('
                det.bookingInvCodeSystem as invoice_id,
                m.matchingDocCode as doc_code,
                SUM(IFNULL(det.receiveAmountTrans,0)) as amount,
                m.matchingConfirmedYN as matching_confirmed
            ')
            ->get();

        foreach ($matchingRows as $r) {
            $iid = (int) $r->invoice_id;
            if (! array_key_exists($iid, $byInvoice)) {
                continue;
            }
            $byInvoice[$iid][] = [
                'document_type' => 'matching',
                'document_code' => $r->doc_code,
                'amount' => (float) ($r->amount ?? 0),
                'document_status' => $this->matchingStatusLabel($r->matching_confirmed),
            ];
        }

        $returnRows = $this->salesReturnBaseQuery($subCompanies)
            ->whereIn('srd.custInvoiceDirectAutoID', $invoiceIds)
            ->groupBy('srd.custInvoiceDirectAutoID', 'sr.id', 'sr.salesReturnCode', 'sr.approvedYN', 'sr.confirmedYN')
            ->selectRaw('
                srd.custInvoiceDirectAutoID as invoice_id,
                sr.salesReturnCode as doc_code,
                SUM(IFNULL(srd.transactionAmount,0) + (IFNULL(srd.transactionAmount,0) * IFNULL(srd.VATPercentage,0) / 100)) as amount,
                sr.approvedYN as sr_approved,
                sr.confirmedYN as sr_confirmed
            ')
            ->get();

        foreach ($returnRows as $r) {
            $iid = (int) $r->invoice_id;
            if (! array_key_exists($iid, $byInvoice)) {
                continue;
            }
            $byInvoice[$iid][] = [
                'document_type' => 'sales_return',
                'document_code' => $r->doc_code,
                'amount' => (float) ($r->amount ?? 0),
                'document_status' => $this->salesReturnStatusLabel($r->sr_approved, $r->sr_confirmed),
            ];
        }

        return $byInvoice;
    }

    public function mapBalanceItem(CustomerInvoiceDirect $invoice, array $docsByInvoice, array $documentTypeLabels): array
    {
        $iid = (int) $invoice->custInvoiceDirectAutoID;
        $dt = isset($invoice->isPerforma) ? (int) $invoice->isPerforma : -1;

        return [
            'invoice_code' => $invoice->bookingInvCode,
            'customer' => optional($invoice->customer)->CustomerName,
            'invoice_type' => $documentTypeLabels[$dt] ?? '',
            'document_no' => $invoice->customerInvoiceNo,
            'invoice_date' => $invoice->bookingDate,
            'warehouse' => optional($invoice->warehouse)->wareHouseDescription,
            'transaction_currency' => optional($invoice->currency)->CurrencyCode,
            'due_date' => $invoice->invoiceDueDate,
            'invoice_amount' => (float) ($invoice->invoice_amount ?? 0),
            'balance_amount' => (float) ($invoice->balance_amount ?? 0),
            'status' => [
                'status' => $invoice->balance_payment_status ?? '',
                'docs' => $docsByInvoice[$iid] ?? [],
            ],
            'created_date_time' => $invoice->createdDateAndTime,
            'created_by' => optional($invoice->createduser)->empName,
            'last_updated_date_time' => $invoice->timestamp,
            'last_updated_by' => optional($invoice->modified_by)->empName,
            'generated_from' => $invoice->generated_from_display ?: '',
        ];
    }


}
