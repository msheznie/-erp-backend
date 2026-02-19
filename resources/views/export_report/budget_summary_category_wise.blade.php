<html>
<body>
<table>
    <thead>
    <tr>
        <td colspan="3"></td>
        <td style="font-weight: bold">{{ trans('custom.report_template_category_wise') }}</td>
        <td colspan="3"></td>
    </tr>
    <tr>
        @php
            $financeYearBy = $entity['finance_year_by'] ?? null;
            $bigginingDate = '';
            $endingDate = '';
            if ($financeYearBy) {
                try {
                    $startRaw = $financeYearBy['bigginingDate'] ?? $financeYearBy['beginningDate'] ?? '';
                    if ($startRaw) {
                        $dt = new DateTime($startRaw);
                        $bigginingDate = $dt->format('d/m/Y');
                    }
                } catch (\Exception $e) {}
                try {
                    $endRaw = $financeYearBy['endingDate'] ?? '';
                    if ($endRaw) {
                        $dt = new DateTime($endRaw);
                        $endingDate = $dt->format('d/m/Y');
                    }
                } catch (\Exception $e) {}
            }
        @endphp
        <td>{{ trans('custom.finance_year') }} : {{ $bigginingDate }} - {{ $endingDate }}</td>
        <td></td>
        <td></td>
        <td></td>
        <td>{{ trans('custom.year') }} : {{ $entity['Year'] ?? '' }}</td>
    </tr>
    <tr>
        @php
            $segmentBy = $entity['segment_by'] ?? null;
            $templateMaster = $entity['template_master'] ?? null;
        @endphp
        <td>{{ trans('custom.segment') }} : {{ (is_array($segmentBy) ? ($segmentBy['ServiceLineDes'] ?? '') : '') }}</td>
        <td></td>
        <td></td>
        <td></td>
        <td>{{ trans('custom.template') }} : {{ (is_array($templateMaster) ? ($templateMaster['description'] ?? '') : '') }}</td>
    </tr>
    <tr><td colspan="6"></td></tr>
    <tr><td colspan="6"></td></tr>
    <tr>
        <th>{{ trans('custom.template_description') }}</th>
        <th>{{ trans('custom.budget_amount') }} ({{ ($rptCurrency->CurrencyCode ?? null) ?: 'USD' }})</th>
        <th>{{ trans('custom.commited_budget') }}</th>
        <th>{{ trans('custom.actual_consumption') }}</th>
        <th>{{ trans('custom.pending_document_amount') }}</th>
        <th>{{ trans('custom.balance') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($reportData ?? [] as $item)
        <tr>
            <td>{{ $item->templateDetailDescription ?? '' }}</td>
            <td>{{ number_format($item->totalRpt ?? 0, 2) }}</td>
            <td>{{ number_format($item->committedAmount ?? 0, 2) }}</td>
            <td>{{ number_format($item->actualConsumption ?? 0, 2) }}</td>
            <td>{{ number_format($item->pendingDocumentAmount ?? 0, 2) }}</td>
            <td>{{ number_format($item->balance ?? 0, 2) }}</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        @php
            $tot = $total ?? [];
        @endphp
        <td>{{ trans('custom.total_amount') }}</td>
        <td>{{ number_format($tot['totalRpt'] ?? 0, 3) }}</td>
        <td>{{ number_format($tot['committedAmount'] ?? 0, 2) }}</td>
        <td>{{ number_format($tot['actualConsumption'] ?? 0, 2) }}</td>
        <td>{{ number_format($tot['pendingDocumentAmount'] ?? 0, 2) }}</td>
        <td>{{ number_format($tot['balance'] ?? 0, 2) }}</td>
    </tr>
    </tfoot>
</table>
</body>
</html>
