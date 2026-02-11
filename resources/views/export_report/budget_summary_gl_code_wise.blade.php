<table>
    @php
        $financeYearBy = $entity['finance_year_by'] ?? null;
        if ($financeYearBy && isset($financeYearBy['bigginingDate'])) {
            $bigginingDt = new DateTime($financeYearBy['bigginingDate']);
            $bigginingDate = $bigginingDt->format('d/m/Y');
        } else {
            $bigginingDate = '-';
        }
        if ($financeYearBy && isset($financeYearBy['endingDate'])) {
            $endingDt = new DateTime($financeYearBy['endingDate']);
            $endingDate = $endingDt->format('d/m/Y');
        } else {
            $endingDate = '-';
        }
    @endphp
    <tr>
        <td colspan="3"></td>
        <td>{{ trans('custom.report_gl_code_wise') }}</td>
        <td colspan="7"></td>
    </tr>
    <tr>
        <td>{{ trans('custom.finance_year') }} : {{ $bigginingDate }} - {{ $endingDate }}</td>
        <td></td>
        <td></td>
        <td></td>
        <td>{{ trans('custom.year') }} : {{ $entity['Year'] ?? '-' }}</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td>{{ trans('custom.segment') }} : {{ isset($entity['segment_by']['ServiceLineDes']) ? $entity['segment_by']['ServiceLineDes'] : '-' }}</td>
        <td></td>
        <td></td>
        <td></td>
        <td>{{ trans('custom.template') }} : {{ isset($entity['template_master']['description']) ? $entity['template_master']['description'] : '-' }}</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
    <tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
    <tr>
        <td>{{ trans('custom.template_description') }}</td>
        <td>{{ trans('custom.gl_code') }}</td>
        <td>{{ trans('custom.account_description') }}</td>
        <td>{{ trans('custom.gl_type') }}</td>
        <td>{{ trans('custom.local_amount') }}</td>
        <td>{{ trans('custom.reporting_amount') }}</td>
        <td>{{ trans('custom.commited_budget') }}</td>
        <td>{{ trans('custom.actual_consumption') }}</td>
        <td>{{ trans('custom.pending_document_amount') }}</td>
        <td>{{ trans('custom.balance') }}</td>
        <td>{{ trans('custom.adjusted_amount') }}</td>
    </tr>
    @foreach($reportData as $item)
    <tr>
        <td>{{ $item->templateDetailDescription ?? '' }}</td>
        <td>{{ $item->AccountCode ?? '' }}</td>
        <td>{{ $item->AccountDescription ?? '' }}</td>
        <td>{{ $item->glCodeType ?? '' }}</td>
        <td>{{ number_format($item->totalLocal ?? 0, $decimalPlaceLocal ?? 3) }}</td>
        <td>{{ number_format($item->totalRpt ?? 0, $decimalPlaceRpt ?? 2) }}</td>
        <td>{{ number_format($item->committedAmount ?? 0, 2) }}</td>
        <td>{{ number_format($item->actuallConsumptionAmount ?? 0, 2) }}</td>
        <td>{{ number_format($item->pendingDocumentAmount ?? 0, 2) }}</td>
        <td>{{ number_format($item->balance ?? 0, 2) }}</td>
        <td>{{ number_format($item->adjusted_amount ?? 0, 2) }}</td>
    </tr>
    @endforeach
    <tr>
        <td></td>
        <td></td>
        <td>{{ trans('custom.total_amount') }}</td>
        <td></td>
        <td>{{ number_format($total['totalLocal'] ?? 0, $decimalPlaceLocal ?? 2) }}</td>
        <td>{{ number_format($total['totalRpt'] ?? 0, $decimalPlaceRpt ?? 3) }}</td>
        <td>{{ number_format($total['committedAmount'] ?? 0, 2) }}</td>
        <td>{{ number_format($total['actuallConsumptionAmount'] ?? 0, 2) }}</td>
        <td>{{ number_format($total['pendingDocumentAmount'] ?? 0, 2) }}</td>
        <td>{{ number_format($total['balance'] ?? 0, 2) }}</td>
        <td></td>
    </tr>
</table>
