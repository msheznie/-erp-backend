<html>
    <table>
        <thead>
        <tr></tr>
        <tr>
            <td colspan="2"></td>
            <td style="text-align: center;"><h1>{{ __('custom.tax_deductibility_report') }}</h1></td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td style="text-align: center;"><h2>{{ $company_name ?? '' }}</h2></td>
        </tr>
        <tr></tr>
        <tr style="font-weight: bold">
            <td>{{ __('custom.start_date') }} : {{ $fromDate }} </td>
            <td>{{ __('custom.end_date') }} :  {{ $toDate }}</td>
            <td>{{ __('custom.currency') }} : {{ $currencyRpt ?? '' }}</td>
        </tr>
        <tr></tr>
        <tr>
            <th>{{ __('custom.serial_no') }}</th>
            <th>{{ __('custom.supplier_name') }}</th>
            <th>{{ __('custom.nature_of_service_executed') }}</th>
            <th>{{ __('custom.supplier_invoice_number') }}</th>
            @if(isset($taxExtraColumn) && is_array($taxExtraColumn) && collect($taxExtraColumn)->where('id', 'supplier_invoice_date')->count() > 0)
                <th>{{ __('custom.supplier_invoice_date') }}</th>
            @endif
            @if(isset($taxExtraColumn) && is_array($taxExtraColumn) && collect($taxExtraColumn)->where('id', 'supplier_invoice_amount')->count() > 0)
                <th>{{ __('custom.supplier_invoice_amount') }}</th>
            @endif
            @if(isset($taxExtraColumn) && is_array($taxExtraColumn) && collect($taxExtraColumn)->where('id', 'currency')->count() > 0)
                <th>{{ __('custom.currency') }}</th>
            @endif
            @if(isset($taxExtraColumn) && is_array($taxExtraColumn) && collect($taxExtraColumn)->where('id', 'payment_voucher_status')->count() > 0)
                <th>{{ __('custom.payment_voucher_status') }}</th>
            @endif
            @if(isset($taxExtraColumn) && is_array($taxExtraColumn) && collect($taxExtraColumn)->where('id', 'wht_bears')->count() > 0)
                <th>{{ __('custom.wht_bears') }}</th>
            @endif
            <th>{{ __('custom.payment_voucher_date') }}</th>
            <th>{{ __('custom.due_date_for_payment_of_withholding_tax') }}</th>
            <th>{{ __('custom.actual_date_of_payment_of_withholding_tax') }}</th>
            <th>{{ __('custom.number_of_months_delay') }}</th>
            <th>{{ __('custom.po_amount') }}</th>
            <th>{{ __('custom.withholding_tax_percentage') }}</th>
            <th>{{ __('custom.withholding_tax_amount') }}</th>
            <th>{{ __('custom.additional_tax') }}</th>
            <th>{{ __('custom.total') }}</th>
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
                        @if($data->paymentVoucherStatus === 1)
                            <span>{{ __('custom.paid') }}</span>
                        @elseif($data->paymentVoucherStatus === 2)
                            <span>{{ __('custom.not_paid') }}</span>
                        @else
                            {{ '' }}
                        @endif
                    </td>
                @endif
                @if(isset($taxExtraColumn) && is_array($taxExtraColumn) && collect($taxExtraColumn)->where('id', 'wht_bears')->count() > 0)
                    <td class="text-center">{{ __('custom.vendor_bears_wht') }}</td>
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