<html>
    <table>
        <thead>
        <tr></tr>
        <tr>
            <td colspan="2"></td>
            <td style="text-align: center;"><h1>Tax Deductibility Report</h1></td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td style="text-align: center;"><h2>{{ $company_name ?? '' }}</h2></td>
        </tr>
        <tr></tr>
        <tr style="font-weight: bold">
            <td>Start Date : {{ $fromDate }} </td>
            <td>End Date :  {{ $toDate }}</td>
            <td>Currency : {{ $currencyRpt ?? '' }}</td>
        </tr>
        <tr></tr>
        <tr>
            <th>Serial No</th>
            <th>Supplier Name</th>
            <th>Nature of Service Executed</th>
            <th>Supplier Invoice Number</th>
            @if(isset($taxExtraColumn) && is_array($taxExtraColumn) && collect($taxExtraColumn)->where('id', 'supplier_invoice_date')->count() > 0)
                <th>Supplier Invoice Date</th>
            @endif
            @if(isset($taxExtraColumn) && is_array($taxExtraColumn) && collect($taxExtraColumn)->where('id', 'supplier_invoice_amount')->count() > 0)
                <th>Supplier Invoice Amount</th>
            @endif
            @if(isset($taxExtraColumn) && is_array($taxExtraColumn) && collect($taxExtraColumn)->where('id', 'currency')->count() > 0)
                <th>Currency</th>
            @endif
            @if(isset($taxExtraColumn) && is_array($taxExtraColumn) && collect($taxExtraColumn)->where('id', 'payment_voucher_status')->count() > 0)
                <th>Payment Voucher Status</th>
            @endif
            @if(isset($taxExtraColumn) && is_array($taxExtraColumn) && collect($taxExtraColumn)->where('id', 'wht_bears')->count() > 0)
                <th>WHT bears</th>
            @endif
            <th>Payment Voucher Date</th>
            <th>Due Date for Payment of Withholding Tax</th>
            <th>Actual Date of Payment of Withholding Tax</th>
            <th>Number of Months Delay</th>
            <th>PO Amount</th>
            <th>Withholding Tax Percentage</th>
            <th>Withholding Tax</th>
            <th>Additional Tax (1% per month)</th>
            <th>Total</th>
        </tr>
        </thead>
        <tbody>

        @foreach($reportData as $index => $data)
            <tr style="border-top: {{ isset($data->borderTop) ? '2px solid #000' : '1px solid #ccc' }}">
                <td class="text-center">{{ $data->bookingInvCode }}</td>
                <td>{{ $data->supplier->supplierName  }}</td>
                <td>{{ $data->comments ?? '' }}</td>
                <td>{{ $data->supplierInvoiceNo ?? '' }}</td>
                @if(isset($taxExtraColumn) && is_array($taxExtraColumn) && collect($taxExtraColumn)->where('id', 'supplier_invoice_date')->count() > 0)
                    <td class="text-center">{{ isset($data->supplierInvoiceDate) ? \Helper::dateFormat($data->supplierInvoiceDate) : '' }}</td>
                @endif
                @if(isset($taxExtraColumn) && is_array($taxExtraColumn) && collect($taxExtraColumn)->where('id', 'supplier_invoice_amount')->count() > 0)
                    <td class="text-right">{{ isset($data->supplierInvoiceAmount) ? round($data->supplierInvoiceAmount, $decimalPlaceRpt) : '' }}</td>
                @endif
                @if(isset($taxExtraColumn) && is_array($taxExtraColumn) && collect($taxExtraColumn)->where('id', 'currency')->count() > 0)
                    <td class="text-center">{{ $data->currency ?? '' }}</td>
                @endif
                @if(isset($taxExtraColumn) && is_array($taxExtraColumn) && collect($taxExtraColumn)->where('id', 'payment_voucher_status')->count() > 0)
                    <td class="text-center">
                        @if($data->paymentVoucherStatus === 0)
                            <span>Not Confirmed</span>
                        @elseif($data->paymentVoucherStatus === 1)
                            <span>Pending to approval</span>
                        @elseif($data->paymentVoucherStatus === 2)
                            <span>Approved</span>
                        @else
                            {{ '' }}
                        @endif
                    </td>
                @endif
                @if(isset($taxExtraColumn) && is_array($taxExtraColumn) && collect($taxExtraColumn)->where('id', 'wht_bears')->count() > 0)
                    <td class="text-center">Vendor bears WHT</td>
                @endif
                <td class="text-center">{{ $data->paymentVoucherDate ? \Helper::dateFormat($data->paymentVoucherDate) : '' }}</td>
                <td class="text-center">{{ \Helper::dateFormat($data->dueDateForPaymentOfWithholdingTax) }}</td>
                <td class="text-center">{{ $data->actualDateOfPaymentOfWithholdingTax ? \Helper::dateFormat($data->actualDateOfPaymentOfWithholdingTax) : '' }}</td>
                <td class="text-center">{{ $data->numberOfMonthsDelay ?? 0 }}</td>
                <td class="text-right">{{ round($data->poAmount, $decimalPlaceRpt) }}</td>
                <td class="text-center">{{ $data->whtPercentage ?? 0 }}</td>
                <td class="text-right">{{ round($data->withholdingTax, $decimalPlaceRpt) }}</td>
                <td class="text-right">{{ round($data->additionalTax, $decimalPlaceRpt) }}</td>
                <td class="text-right">{{ round($data->total, $decimalPlaceRpt) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</html> 