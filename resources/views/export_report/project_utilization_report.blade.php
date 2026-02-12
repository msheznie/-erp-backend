<?php
use Carbon\Carbon;
use Carbon\CarbonPeriod;

$decimalPoint = 2;
$CurrencyName = 'N/A';
$CurrencyCode = 'N/A';
if (isset($companyReportingCurrency) && is_object($companyReportingCurrency)) {
    $decimalPoint = $companyReportingCurrency->DecimalPlaces ?? 2;
    $CurrencyName = $companyReportingCurrency->CurrencyName ?? 'N/A';
    $CurrencyCode = $companyReportingCurrency->CurrencyCode ?? 'N/A';
}
?>
<html>
<table border="1" cellpadding="3" cellspacing="0" style="border-collapse: collapse;">
    <thead>
        <tr></tr>
        <tr>
            <th colspan="6" align="center" style="font-size: 14px; font-weight: bold;">{{ $reportTittle ?? '' }}</th>
        </tr>
        <tr>
            <th colspan="6" align="center" style="font-size: 14px; font-weight: bold;">{{ $companyName ?? '' }}</th>
        </tr>
        <tr></tr>
        <tr>
            <th colspan="2" style="font-size: 12px;">{{ __('custom.date_from') }}: {{ $fromDate ?? '' }}</th>
            <th colspan="2" style="font-size: 12px;">{{ __('custom.date_to') }}: {{ $toDate ?? '' }}</th>
        </tr>
        <tr></tr>
        <tr>
            <th>{{ __('custom.project_details') }}</th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
        <tr style="background-color: #e4e5e6; font-weight: bold;">
            <th>{{ __('custom.gl_code') }}</th>
            <th>{{ __('custom.gl_description') }}</th>
            <th>{{ __('custom.document_number') }}</th>
            <th>{{ __('custom.document_date') }}</th>
            <th>{{ __('custom.segment') }}</th>
            <th>{{ __('custom.amount') }}</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ __('custom.project_description') }}</td>
            <td> - {{ $projectDetail->description ?? '' }}</td>
            <td>{{ __('custom.project_currency') }}</td>
            <td> - {{ $projectDetail->currency->CurrencyName ?? '' }}</td>
            <td>{{ __('custom.project_budget') }}</td>
            <td> - {{ round($projectAmount ?? 0, $decimalPoint) }} ({{ $CurrencyCode }})</td>
        </tr>
        <tr>
            <td>{{ __('custom.segment') }}</td>
            <td> - {{ $projectDetail->service_line->ServiceLineDes ?? '' }}</td>
            <td>{{ __('custom.reporting_currency') }}</td>
            <td> - {{ $CurrencyName }}</td>
            <td>{{ __('custom.balance_amount') }}</td>
            <td> - {{ round($closingBalance ?? 0, $decimalPoint) }} ({{ $CurrencyCode }})</td>
        </tr>
        <tr></tr>
        <tr>
            <th>{{ __('custom.opening_balance') }}</th>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{ round($openingBalance ?? 0, $decimalPoint) }}</td>
        </tr>
        @foreach ($detailsPOWise ?? [] as $item)
            @php
                $date = '';
                if ($item->documentSystemID == 2 && isset($item->purchase_order_detail->approvedDate)) {
                    $date = explode(' ', $item->purchase_order_detail->approvedDate);
                } elseif ($item->documentSystemID == 15 && isset($item->debit_note_detail->debitNoteDate)) {
                    $date = explode(' ', $item->debit_note_detail->debitNoteDate);
                } elseif ($item->documentSystemID == 19 && isset($item->credit_note_detail->creditNoteDate)) {
                    $date = explode(' ', $item->credit_note_detail->creditNoteDate);
                } elseif ($item->documentSystemID == 4 && isset($item->direct_payment_voucher_detail->postedDate)) {
                    $date = explode(' ', $item->direct_payment_voucher_detail->postedDate);
                } elseif ($item->documentSystemID == 3 && isset($item->grv_master_detail->grvDate)) {
                    $date = explode(' ', $item->grv_master_detail->grvDate);
                } elseif ($item->documentSystemID == 17 && isset($item->jv_master_detail->JVdate)) {
                    $date = explode(' ', $item->jv_master_detail->JVdate);
                } elseif ($item->documentSystemID == 11 && isset($item->supplier_invoice_master->bookingDate)) {
                    $date = explode(' ', $item->supplier_invoice_master->bookingDate);
                }
                $date = $date ? (new Carbon($date[0]))->format('d/m/Y') : '';
            @endphp
            <tr>
                <td style="text-align: left;">{{ $item->GLCode ?? '' }}</td>
                <td>{{ $item->chart_of_account->AccountDescription ?? '' }}</td>
                <td>{{ $item->documentCode ?? '' }}</td>
                <td>{{ $date }}</td>
                <td>{{ $item->segment_by->ServiceLineDes ?? '' }}</td>
                <td>{{ round($item->consumedRptAmount ?? 0, $decimalPoint) }}</td>
            </tr>
        @endforeach
        <tr>
            <th>{{ __('custom.total_consumption') }}</th>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{ round($budgetConsumptionAmount ?? 0, $decimalPoint) }}</td>
        </tr>
        <tr>
            <th>{{ __('custom.closing_balance') }}</th>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{ round($closingBalance ?? 0, $decimalPoint) }}</td>
        </tr>
    </tbody>
</table>
</html>